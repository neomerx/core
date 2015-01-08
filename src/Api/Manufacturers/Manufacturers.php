<?php namespace Neomerx\Core\Api\Manufacturers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Api\Addresses\Addresses;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\ManufacturerProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Manufacturers implements ManufacturersInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Manufacturer.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Manufacturer
     */
    private $manufacturer;

    /**
     * @var ManufacturerProperties
     */
    private $properties;

    /**
     * @var Addresses
     */
    private $addressApi;

    /**
     * @var Language
     */
    private $language;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        Manufacturer::FIELD_CODE  => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Manufacturer::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Manufacturer::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Manufacturer           $manufacturer
     * @param ManufacturerProperties $properties
     * @param Addresses              $addressApi
     * @param Language               $language
     */
    public function __construct(
        Manufacturer $manufacturer,
        ManufacturerProperties $properties,
        Addresses $addressApi,
        Language $language
    ) {
        $this->manufacturer = $manufacturer;
        $this->properties   = $properties;
        $this->addressApi   = $addressApi;
        $this->language     = $language;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);
        !empty($propertiesInput) ?: S\throwEx(new InvalidArgumentException(Manufacturer::FIELD_PROPERTIES));

        $addressInput = S\array_get_value($input, self::PARAM_ADDRESS);
        !empty($addressInput) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ADDRESS));
        unset($input[self::PARAM_ADDRESS]);

        $manufacturer = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create address, update input and add resource
            $input[Address::FIELD_ID] = $this->addressApi->create($addressInput)->{Address::FIELD_ID};
            /** @var \Neomerx\Core\Models\Manufacturer $manufacturer */
            $manufacturer = $this->manufacturer->createOrFailResource($input);
            Permissions::check($manufacturer, Permission::create());
            $manufacturerId = $manufacturer->{Manufacturer::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge([
                    ManufacturerProperties::FIELD_ID_MANUFACTURER => $manufacturerId,
                    ManufacturerProperties::FIELD_ID_LANGUAGE     => $languageId
                ], $propertyInput));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        if ($manufacturer !== null) {
            Event::fire(new ManufacturerArgs(self::EVENT_PREFIX . 'created', $manufacturer));
        }

        return $manufacturer;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\Manufacturer $manufacturer */
        /** @noinspection PhpUndefinedMethodInspection */
        $manufacturer = $this->manufacturer->selectByCode($code)->withAddress()->withProperties()->firstOrFail();
        Permissions::check($manufacturer, Permission::view());
        return $manufacturer;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        // get input for properties
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        $addressInput = S\array_get_value($input, self::PARAM_ADDRESS);
        unset($input[self::PARAM_ADDRESS]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // update manufacturer
            /** @var \Neomerx\Core\Models\Manufacturer $manufacturer */
            $manufacturer = $this->manufacturer->selectByCode($code)->firstOrFail();
            // we always check manufacturer as later we change its properties. Don't move into 'if'.
            Permissions::check($manufacturer, Permission::edit());
            if (!empty($input)) {
                $manufacturer->updateOrFail($input);
            }

            // update address
            if (!empty($addressInput)) {
                $this->addressApi->updateModel($manufacturer->address, $addressInput);
            }

            // update language properties
            $manufacturerId = $manufacturer->{Manufacturer::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate(
                    [Manufacturer::FIELD_ID => $manufacturerId, Language::FIELD_ID => $languageId],
                    $propertyInput
                );
                /** @noinspection PhpUndefinedMethodInspection */
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ManufacturerArgs(self::EVENT_PREFIX . 'updated', $manufacturer));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\Manufacturer $manufacturer */
        $manufacturer = $this->manufacturer->selectByCode($code)->firstOrFail();

        Permissions::check($manufacturer, Permission::delete());

        $manufacturer->deleteOrFail();

        Event::fire(new ManufacturerArgs(self::EVENT_PREFIX . 'deleted', $manufacturer));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->manufacturer->newQuery()->withAddress()->withProperties();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $manufacturers = $builder->get();

        foreach ($manufacturers as $manufacturer) {
            /** @var \Neomerx\Core\Models\Manufacturer $manufacturer */
            Permissions::check($manufacturer, Permission::view());
        }

        return $manufacturers;
    }
}
