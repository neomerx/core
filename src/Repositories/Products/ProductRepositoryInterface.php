<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct    $baseProduct
     * @param Category       $category
     * @param ProductTaxType $taxType
     * @param array          $attributes
     *
     * @return Product
     */
    public function instance(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
    );

    /**
     * @param Product             $product
     * @param BaseProduct|null    $baseProduct
     * @param Category|null       $category
     * @param ProductTaxType|null $taxType
     * @param array|null          $attributes
     *
     */
    public function fill(
        Product $product,
        BaseProduct $baseProduct = null,
        Category $category = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    );

    /**
     * @param BaseProduct    $baseProduct
     * @param Category       $category
     * @param ProductTaxType $taxType
     * @param array          $attributes
     *
     * @return Product
     */
    public function create(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
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
