<?php namespace Neomerx\Core\Api\Suppliers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Supplier as Model;
use \Neomerx\Core\Models\Address as AddressModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Addresses\Addresses as AddressesApi;
use \Neomerx\Core\Models\SupplierProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Suppliers implements SuppliersInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Supplier.';
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
            /** @var Model $supplier */
            $supplier = $this->model->createOrFailResource($input);
            Permissions::check($supplier, Permission::create());
            $supplierId = $supplier->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge([
                    PropertiesModel::FIELD_ID_SUPPLIER => $supplierId,
                    PropertiesModel::FIELD_ID_LANGUAGE => $languageId
                ], $propertyInput));
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
        /** @var Model $supplier */
        /** @noinspection PhpUndefinedMethodInspection */
        $supplier = $this->model->selectByCode($code)->withAddress()->withProperties()->firstOrFail();
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
            /** @var Model $supplier */
            $supplier = $this->model->selectByCode($code)->firstOrFail();
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
            $resourceId = $supplier->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var PropertiesModel $property */
                $property = $this->properties->updateOrCreate(
                    [Model::FIELD_ID => $resourceId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $supplier */
        $supplier = $this->model->selectByCode($code)->firstOrFail();

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
        $builder = $this->model->newQuery()->withAddress()->withProperties();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), self::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $suppliers = $builder->get();

        foreach ($suppliers as $supplier) {
            /** @var Model $supplier */
            Permissions::check($supplier, Permission::view());
        }

        return $suppliers;
    }
}
