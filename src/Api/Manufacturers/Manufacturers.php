<?php namespace Neomerx\Core\Api\Manufacturers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Manufacturer as Model;
use \Neomerx\Core\Models\Address as AddressModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Addresses\Addresses as AddressesApi;
use \Neomerx\Core\Models\ManufacturerProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Manufacturers implements ManufacturersInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Manufacturer.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var PropertiesModel
     */
    private $properties;

    /**
     * @var AddressesApi
     */
    private $addressApi;

    /**
     * @var LanguageModel
     */
    private $language;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    private static $searchRules = [
        Model::FIELD_CODE         => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Model           $model
     * @param PropertiesModel $properties
     * @param AddressesApi    $addressApi
     * @param LanguageModel   $language
     */
    public function __construct(
        Model $model,
        PropertiesModel $properties,
        AddressesApi $addressApi,
        LanguageModel $language
    ) {
        $this->model      = $model;
        $this->properties = $properties;
        $this->addressApi = $addressApi;
        $this->language   = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);
        !empty($propertiesInput) ?: S\throwEx(new InvalidArgumentException(Model::FIELD_PROPERTIES));

        $addressInput = S\array_get_value($input, self::PARAM_ADDRESS);
        !empty($addressInput) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ADDRESS));
        unset($input[self::PARAM_ADDRESS]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create address, update input and add resource
            $input[AddressModel::FIELD_ID] = $this->addressApi->create($addressInput)->{AddressModel::FIELD_ID};
            /** @var Model $manufacturer */
            $manufacturer = $this->model->createOrFailResource($input);
            Permissions::check($manufacturer, Permission::create());
            $manufacturerId = $manufacturer->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge([
                    PropertiesModel::FIELD_ID_MANUFACTURER => $manufacturerId,
                    PropertiesModel::FIELD_ID_LANGUAGE     => $languageId
                ], $propertyInput));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ManufacturerArgs(self::EVENT_PREFIX . 'created', $manufacturer));

        return $manufacturer;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $manufacturer */
        /** @noinspection PhpUndefinedMethodInspection */
        $manufacturer = $this->model->selectByCode($code)->withAddress()->withProperties()->firstOrFail();
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
            /** @var Model $manufacturer */
            $manufacturer = $this->model->selectByCode($code)->firstOrFail();
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
            $manufacturerId = $manufacturer->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate(
                    [Model::FIELD_ID => $manufacturerId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $manufacturer */
        $manufacturer = $this->model->selectByCode($code)->firstOrFail();

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
        $builder = $this->model->newQuery()->withAddress()->withProperties();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), self::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $manufacturers = $builder->get();

        foreach ($manufacturers as $manufacturer) {
            /** @var Model $manufacturer */
            Permissions::check($manufacturer, Permission::view());
        }

        return $manufacturers;
    }
}
