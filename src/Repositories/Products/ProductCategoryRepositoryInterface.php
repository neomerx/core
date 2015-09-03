<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ProductCategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product  $product
     * @param Category $category
     * @param array    $attributes
     *
     * @return ProductCategory
     */
    public function createWithObjects(Product $product, Category $category, array $attributes);

    /**
     * @param int   $productId
     * @param int   $categoryId
     * @param array $attributes
     *
     * @return ProductCategory
     */
    public function create($productId, $categoryId, array $attributes);

    /**
     * @param ProductCategory $resource
     * @param Product|null    $product
     * @param Category|null   $category
     * @param array|null      $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ProductCategory $resource,
        Product $product = null,
        Category $category = null,
        array $attributes = null
    );

    /**
     * @param ProductCategory $resource
     * @param int|null        $productId
     * @param int|null        $categoryId
     * @param array|null      $attributes
     *
     * @return void
     */
    public function update(
        ProductCategory $resource,
        $productId = null,
        $categoryId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return ProductCategory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
