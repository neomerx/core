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
    public function createWithObjects(Product $product, Product $related);

    /**
     * @param int $productId
     * @param int $relatedId
     *
     * @return ProductRelated
     */
    public function create($productId, $relatedId);

    /**
     * @param ProductRelated $resource
     * @param Product|null   $product
     * @param Product|null   $related
     *
     * @return void
     */
    public function updateWithObjects(ProductRelated $resource, Product $product = null, Product $related = null);

    /**
     * @param ProductRelated $resource
     * @param int|null   $productId
     * @param int|null   $relatedId
     *
     * @return void
     */
    public function update(ProductRelated $resource, $productId = null, $relatedId = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ProductRelated
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
