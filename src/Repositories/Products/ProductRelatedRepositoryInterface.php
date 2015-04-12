<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\ProductRelated;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ProductRelatedRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product $product
     * @param Product $related
     *
     * @return ProductRelated
     */
    public function instance(Product $product, Product $related);

    /**
     * @param ProductRelated $resource
     * @param Product|null   $product
     * @param Product|null   $related
     *
     * @return void
     */
    public function fill(ProductRelated $resource, Product $product = null, Product $related = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ProductRelated
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
