<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Aspect;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ProductRepository extends CodeBasedResourceRepository implements ProductRepositoryInterface
{
    /**
     * @var AspectRepositoryInterface
     */
    private $aspectRepo;

    /**
     * @inheritdoc
     */
    public function __construct(AspectRepositoryInterface $aspectRepo)
    {
        parent::__construct(Product::class);
        $this->aspectRepo = $aspectRepo;
    }

    /**
     * @inheritdoc
     */
    public function instance(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
    ) {
        /** @var Product $product */
        $product = $this->makeModel();
        $this->fill($product, $baseProduct, $category, $taxType, $attributes);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Product $product,
        BaseProduct $baseProduct = null,
        Category $category = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    ) {
        /** @var BaseProduct $baseProduct */
        if ($baseProduct !== null) {
            $product->setAttribute(Product::FIELD_SKU, $baseProduct->getAttribute(BaseProduct::FIELD_SKU));
        }

        $this->fillModel($product, [
            Product::FIELD_ID_BASE_PRODUCT          => $baseProduct,
            Product::FIELD_ID_CATEGORY_DEFAULT => $category,
            Product::FIELD_ID_PRODUCT_TAX_TYPE => $taxType,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
    ) {
        $defaultProduct = $baseProduct->getDefaultProduct();
        // for just created base products there is no default product yet
        $defaultAspects = $defaultProduct !== null ? $defaultProduct->{Product::FIELD_ASPECTS} : [];

        $product = $this->instance($baseProduct, $category, $taxType, $attributes);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            $product->saveOrFail();
            foreach ($defaultAspects as $aspect) {
                /** @var Aspect $aspect */
                $this->aspectRepo
                    ->instance($baseProduct, $aspect->value, $aspect->attributesToArray(), $product)
                    ->saveOrFail();
            }

            $allExecutedOk = true;
        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }

        return $product;
    }
}
