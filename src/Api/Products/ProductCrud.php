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
use \Neomerx\Core\Api\Traits\InputParserTrait;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Exceptions\NullArgumentException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductCrud
{
    use InputParserTrait;
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

            /** @var \Neomerx\Core\Models\Product $product */
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
        /** @var \Neomerx\Core\Models\Product $product */
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
            /** @var \Neomerx\Core\Models\Product $product */
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
            /** @var \Neomerx\Core\Models\Product $product */
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
        /** @var \Neomerx\Core\Models\Product $product */
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
        $this->replaceInputCodeWithId(
            $input,
            Products::PARAM_MANUFACTURER_CODE,
            $this->manufacturerModel,
            Manufacturer::FIELD_ID,
            Product::FIELD_ID_MANUFACTURER
        );

        $this->replaceInputCodeWithId(
            $input,
            Products::PARAM_TAX_TYPE_CODE,
            $this->taxTypeModel,
            ProductTaxType::FIELD_ID,
            Product::FIELD_ID_PRODUCT_TAX_TYPE
        );

        $this->replaceInputCodeWithId(
            $input,
            Products::PARAM_DEFAULT_CATEGORY_CODE,
            $this->categoryModel,
            Category::FIELD_ID,
            Product::FIELD_ID_CATEGORY_DEFAULT
        );

        return $input;
    }
}
