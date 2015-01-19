<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ProductRepositoryInterface extends RepositoryInterface
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
        array $attributes
    );

    /**
     * @param Product             $product
     * @param Category|null       $category
     * @param Manufacturer|null   $manufacturer
     * @param ProductTaxType|null $taxType
     * @param array|null          $attributes
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

    /**
     * @param string $sku
     * @param array  $relations
     * @param array  $columns
     *
     * @return Product
     */
    public function read($sku, array $relations = [], array $columns = ['*']);
}
