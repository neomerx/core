<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductTaxType;

interface ProductRepositoryInterface
{
    /**
     * @param Category       $category
     * @param Manufacturer   $manufacturer
     * @param ProductTaxType $taxType
     * @param array          $attributes
     *
     * @return Product
     */
    public function instance(
        Category $category,
        Manufacturer $manufacturer,
        ProductTaxType $taxType,
        array $attributes = null
    );

    /**
     * @param Product        $product
     * @param Category       $category
     * @param Manufacturer   $manufacturer
     * @param ProductTaxType $taxType
     * @param array          $attributes
     *
     * @return void
     */
    public function fill(
        Product $product,
        Category $category = null,
        Manufacturer $manufacturer = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    );
}
