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
    public function createWithObjects(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
    );

    /**
     * @param int   $baseProductId
     * @param int   $categoryId
     * @param int   $taxTypeId
     * @param array $attributes
     *
     * @return Product
     */
    public function create(
        $baseProductId,
        $categoryId,
        $taxTypeId,
        array $attributes
    );

    /**
     * @param Product             $product
     * @param Category|null       $category
     * @param ProductTaxType|null $taxType
     * @param array|null          $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        Product $product,
        Category $category = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    );

    /**
     * @param Product    $product
     * @param int|null   $categoryId
     * @param int|null   $taxTypeId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(
        Product $product,
        $categoryId = null,
        $taxTypeId = null,
        array $attributes = null
    );

    /**
     * @param string $index
     * @param array  $relations
     * @param array  $columns
     *
     * @return Product
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
