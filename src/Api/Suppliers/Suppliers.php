<?php namespace Neomerx\Core\Api\Suppliers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Api\Addresses\Addresses;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\SupplierProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Suppliers implements SuppliersInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Supplier.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @var SupplierProperties
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
        Supplier::FIELD_CODE      => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Supplier::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Supplier::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Supplier           $supplier
     * @param SupplierProperties $properties
     * @param Addresses          $addressApi
     * @param Language           $language
     */
    public function __construct(
        Supplier $supplier,
        SupplierProperties $properties,
        Addresses $addressApi,
        Language $language
    ) {
        $this->supplier      = $supplier;
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
        !empty($propertiesInput) ?: S\throwEx(new InvalidArgumentException(Supplier::FIELD_PROPERTIES));

        $addressInput = S\array_get_value($input, self::PARAM_ADDRESS);
        !empty($addressInput) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ADDRESS));
        unset($input[self::PARAM_ADDRESS]);

        $supplier = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create address, update input and add resource
            $input[Address::FIELD_ID] = $this->addressApi->create($addressInput)->{Address::FIELD_ID};
            /** @var Supplier $supplier */
            $supplier = $this->supplier->createOrFailResource($input);
            Permissions::check($supplier, Permission::create());
            $supplierId = $supplier->{Supplier::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge($propertyInput, [
                    SupplierProperties::FIELD_ID_SUPPLIER => $supplierId,
                    SupplierProperties::FIELD_ID_LANGUAGE => $languageId
                ]));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new SupplierArgs(self::EVENT_PREFIX . 'created', $supplier));

        return $supplier;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Supplier $supplier */
        /** @noinspection PhpUndefinedMethodInspection */
        $supplier = $this->supplier->selectByCode($code)->withAddress()->withProperties()->firstOrFail();
        Permissions::check($supplier, Permission::view());
        return $supplier;
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
            // update resource
            /** @var Supplier $supplier */
            $supplier = $this->supplier->selectByCode($code)->firstOrFail();
            // always check supplier because later we update its properties
            Permissions::check($supplier, Permission::edit());
            if (!empty($input)) {
                $supplier->updateOrFail($input);
            }

            // update address
            if (!empty($addressInput)) {
                $this->addressApi->updateModel($supplier->address, $addressInput);
            }

            // update language properties
            $resourceId = $supplier->{Supplier::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var SupplierProperties $property */
                $property = $this->properties->updateOrCreate(
                    [
                        SupplierProperties::FIELD_ID_SUPPLIER => $resourceId,
                        SupplierProperties::FIELD_ID_LANGUAGE => $languageId
                    ],
                    $propertyInput
                );
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new SupplierArgs(self::EVENT_PREFIX . 'updated', $supplier));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Supplier $supplier */
        $supplier = $this->supplier->selectByCode($code)->firstOrFail();

        Permissions::check($supplier, Permission::delete());

        $supplier->deleteOrFail();

        Event::fire(new SupplierArgs(self::EVENT_PREFIX . 'deleted', $supplier));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->supplier->newQuery()->withAddress()->withProperties();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $suppliers = $builder->get();

        foreach ($suppliers as $supplier) {
            /** @var Supplier $supplier */
            Permissions::check($supplier, Permission::view());
        }

        return $suppliers;
    }
}
