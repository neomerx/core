<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Exceptions\NullArgumentException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

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
        Product::FIELD_MANUFACTURER,
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
        Product::FIELD_SKU        => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Product::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Product::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var ProductProperties
     */
    private $propertiesModel;

    /**
     * @var Category
     */
    private $categoryModel;

    /**
     * @var Manufacturer
     */
    private $manufacturerModel;

    /**
     * @var ProductTaxType
     */
    private $taxTypeModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @param Product           $product
     * @param ProductProperties $properties
     * @param Category          $category
     * @param Manufacturer      $manufacturer
     * @param ProductTaxType    $taxType
     * @param Language          $language
     */
    public function __construct(
        Product $product,
        ProductProperties $properties,
        Category $category,
        Manufacturer $manufacturer,
        ProductTaxType $taxType,
        Language $language
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
     * @return Product
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

            /** @var Product $product */
            $product = $this->productModel->createOrFailResource($input);
            Permissions::check($product, Permission::create());
            $productId = $product->{Product::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge($propertyInput, [
                    ProductProperties::FIELD_ID_PRODUCT  => $productId,
                    ProductProperties::FIELD_ID_LANGUAGE => $languageId,
                ]));
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
     * @return Product
     */
    public function read($code)
    {
        /** @noinspection PhpParamsInspection */
        /** @var Product $product */
        $product = $this->productModel->selectByCode($code)->with(static::$relations)->firstOrFail();
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
        $builder = $this->productModel->newQuery()->with(static::$relations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $products = $builder->get();

        foreach ($products as $product) {
            /** @var Product $product */
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
            /** @var Product $product */
            $product = $this->productModel->selectByCode($sku)->firstOrFail();
            Permissions::check($product, Permission::edit());
            empty($input) ?: $product->updateOrFail($input);

            // update language properties
            $productId = $product->{Product::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->propertiesModel->updateOrCreate($propertyInput, [
                    ProductProperties::FIELD_ID_PRODUCT  => $productId,
                    ProductProperties::FIELD_ID_LANGUAGE => $languageId
                ]);
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
        /** @var Product $product */
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
            $extraFields[Manufacturer::FIELD_ID] = $manufacturer->{Manufacturer::FIELD_ID};
        }

        if (isset($input[Products::PARAM_TAX_TYPE_CODE])) {
            $taxType = $this->taxTypeModel
                ->selectByCode($input[Products::PARAM_TAX_TYPE_CODE])->firstOrFail();
            unset($input[Products::PARAM_TAX_TYPE_CODE]);
            $extraFields[ProductTaxType::FIELD_ID] = $taxType->{ProductTaxType::FIELD_ID};
        }

        if (isset($input[Products::PARAM_DEFAULT_CATEGORY_CODE])) {
            /** @var Category $category */
            $category = $this->categoryModel
                ->selectByCode($input[Products::PARAM_DEFAULT_CATEGORY_CODE])->firstOrFail();
            unset($input[Products::PARAM_DEFAULT_CATEGORY_CODE]);
            $extraFields['id_category_default'] = $category->getKey();
        }

        return array_merge($input, $extraFields);
    }
}
