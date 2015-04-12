<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ProductRepository extends CodeBasedResourceRepository implements ProductRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Product::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Category $category,
        Manufacturer $manufacturer,
        ProductTaxType $taxType,
        array $attributes
    ) {
        /** @var \Neomerx\Core\Models\Product $product */
        $product = $this->makeModel();
        $this->fill($product, $category, $manufacturer, $taxType, $attributes);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Product $product,
        Category $category = null,
        Manufacturer $manufacturer = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    ) {
        $this->fillModel($product, [
            Product::FIELD_ID_CATEGORY_DEFAULT => $category,
            Product::FIELD_ID_MANUFACTURER     => $manufacturer,
            Product::FIELD_ID_PRODUCT_TAX_TYPE => $taxType,
        ], $attributes);
    }
}
