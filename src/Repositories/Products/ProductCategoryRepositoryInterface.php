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
    public function instance(Product $product, Category $category, array $attributes);

    /**
     * @param ProductCategory $resource
     * @param Product|null    $product
     * @param Category|null   $category
     * @param array|null      $attributes
     *
     * @return void
     */
    public function fill(
        ProductCategory $resource,
        Product $product = null,
        Category $category = null,
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
