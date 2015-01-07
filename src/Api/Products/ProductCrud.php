<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Category as CategoryModel;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Exceptions\NullArgumentException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\ProductTaxType as TaxTypeModel;
use \Neomerx\Core\Models\Manufacturer as ManufacturerModel;
use \Neomerx\Core\Models\ProductProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductCrud
{
    use LanguagePropertiesTrait;

    /**
     * @var array
     */
    public static $relations = [
        'manufacturer',
        'defaultCategory',
        'properties.language',
        'taxType',
    ];

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        'sku'     => 'string',
        'created' => ['date', 'created_at'],
        'updated' => ['date', 'updated_at'],
        'skip'    => 'limit',
        'take'    => 'limit',
    ];

    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var PropertiesModel
     */
    private $propertiesModel;

    /**
     * @var CategoryModel
     */
    private $categoryModel;

    /**
     * @var ManufacturerModel
     */
    private $manufacturerModel;

    /**
     * @var TaxTypeModel
     */
    private $taxTypeModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @param Model             $product
     * @param PropertiesModel   $properties
     * @param CategoryModel     $category
     * @param ManufacturerModel $manufacturer
     * @param TaxTypeModel      $taxType
     * @param LanguageModel     $language
     */
    public function __construct(
        Model $product,
        PropertiesModel $properties,
        CategoryModel $category,
        ManufacturerModel $manufacturer,
        TaxTypeModel $taxType,
        LanguageModel $language
    ) {
        $this->productModel      = $product;
        $this->propertiesModel   = $properties;
        $this->categoryModel     = $category;
        $this->manufacturerModel = $manufacturer;
        $this->taxTypeModel      = $taxType;
        $this->languageModel     = $language;
    }

    /**
     * Create resource.
     *
     * @param array $input
     *
     * @throws NullArgumentException
     * @throws InvalidArgumentException
     *
     * @return Model
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        // check language properties are not empty
        count($propertiesInput) > 0 ?: S\throwEx(new InvalidArgumentException(Products::PARAM_PROPERTIES));

        // check input params are set
        $isManufacturerSet = isset($input[Products::PARAM_MANUFACTURER_CODE]);
        $isManufacturerSet ?: S\throwEx(new NullArgumentException(Products::PARAM_MANUFACTURER_CODE));

        $isTaxTypeSet = isset($input[Products::PARAM_TAX_TYPE_CODE]);
        $isTaxTypeSet ?: S\throwEx(new NullArgumentException(Products::PARAM_TAX_TYPE_CODE));

        $isDefCategorySet = isset($input[Products::PARAM_DEFAULT_CATEGORY_CODE]);
        $isDefCategorySet ?: S\throwEx(new NullArgumentException(Products::PARAM_DEFAULT_CATEGORY_CODE));

        $input = $this->replaceCodesWithIds($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $product */
            $product = $this->productModel->createOrFailResource($input);
            Permissions::check($product, Permission::create());
            $productId = $product->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge(
                    [Model::FIELD_ID => $productId, LanguageModel::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'created', $product));

        return $product;
    }

    /**
     * Read resource by identifier.
     *
     * @param string $code
     *
     * @return Model
     */
    public function read($code)
    {
        /** @noinspection PhpParamsInspection */
        /** @var Model $product */
        $product = $this->productModel->selectByCode($code)->with(self::$relations)->firstOrFail();
        Permissions::check($product, Permission::view());
        return $product;
    }

    /**
     * Search products.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpParamsInspection */
        $builder = $this->productModel->newQuery()->with(self::$relations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $products = $builder->get();

        foreach ($products as $product) {
            /** @var Model $product */
            Permissions::check($product, Permission::view());
        }

        return $products;
    }

    /**
     * Update product.
     *
     * @param string $sku
     * @param array  $input
     *
     * @return void
     */
    public function update($sku, array $input)
    {
        // get input for properties
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        $input = $this->replaceCodesWithIds($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // update resource
            /** @var Model $product */
            $product = $this->productModel->selectByCode($sku)->firstOrFail();
            Permissions::check($product, Permission::edit());
            empty($input) ?: $product->updateOrFail($input);

            // update language properties
            $productId = $product->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->propertiesModel->updateOrCreate(
                    [Model::FIELD_ID => $productId, LanguageModel::FIELD_ID => $languageId],
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

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updated', $product));
    }

    /**
     * Remove product.
     *
     * @param string $sku
     *
     * @return void
     */
    public function delete($sku)
    {
        /** @var Model $product */
        $product = $this->productModel->selectByCode($sku)->firstOrFail();
        Permissions::check($product, Permission::delete());
        $product->deleteOrFail();

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deleted', $product));
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function replaceCodesWithIds(array $input)
    {
        $extraFields = [];

        if (isset($input[Products::PARAM_MANUFACTURER_CODE])) {
            $manufacturer = $this->manufacturerModel
                ->selectByCode($input[Products::PARAM_MANUFACTURER_CODE])->firstOrFail();
            unset($input[Products::PARAM_MANUFACTURER_CODE]);
            $extraFields[ManufacturerModel::FIELD_ID] = $manufacturer->{ManufacturerModel::FIELD_ID};
        }

        if (isset($input[Products::PARAM_TAX_TYPE_CODE])) {
            $taxType = $this->taxTypeModel
                ->selectByCode($input[Products::PARAM_TAX_TYPE_CODE])->firstOrFail();
            unset($input[Products::PARAM_TAX_TYPE_CODE]);
            $extraFields[TaxTypeModel::FIELD_ID] = $taxType->{TaxTypeModel::FIELD_ID};
        }

        if (isset($input[Products::PARAM_DEFAULT_CATEGORY_CODE])) {
            /** @var CategoryModel $category */
            $category = $this->categoryModel
                ->selectByCode($input[Products::PARAM_DEFAULT_CATEGORY_CODE])->firstOrFail();
            unset($input[Products::PARAM_DEFAULT_CATEGORY_CODE]);
            $extraFields['id_category_default'] = $category->getKey();
        }

        return array_merge($input, $extraFields);
    }
}
