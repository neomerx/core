<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductImage;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ProductImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct   $base
     * @param Image         $image
     * @param array         $attributes
     * @param Nullable|null $product Product
     *
     * @return ProductImage
     */
    public function createWithObjects(BaseProduct $base, Image $image, array $attributes, Nullable $product = null);

    /**
     * @param int           $baseProductId
     * @param int           $imageId
     * @param array         $attributes
     * @param Nullable|null $productId
     *
     * @return ProductImage
     */
    public function create($baseProductId, $imageId, array $attributes, Nullable $productId = null);

    /**
     * @param ProductImage     $resource
     * @param BaseProduct|null $base
     * @param Image|null       $image
     * @param array|null       $attributes
     * @param Nullable|null    $product Product
     *
     * @return void
     */
    public function updateWithObjects(
        ProductImage $resource,
        BaseProduct $base = null,
        Image $image = null,
        array $attributes = null,
        Nullable $product = null
    );

    /**
     * @param ProductImage  $resource
     * @param int|null      $baseProductId
     * @param int|null      $imageId
     * @param array|null    $attributes
     * @param Nullable|null $productId
     *
     * @return void
     */
    public function update(
        ProductImage $resource,
        $baseProductId = null,
        $imageId = null,
        array $attributes = null,
        Nullable $productId = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return ProductImage
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);

    /**
     * @param ProductImage $image
     *
     * @return void
     */
    public function setAsCover(ProductImage $image);

    /**
     * Get product images for base product.
     *
     * @param int   $baseProductId
     * @param array $relations
     * @param array $columns
     *
     * @return Collection
     */
    public function getBaseProductImages($baseProductId, array $relations = [], array $columns = ['*']);

    /**
     * Get product image for base product.
     *
     * @param int   $baseProductId
     * @param int   $productImageId
     * @param array $relations
     * @param array $columns
     *
     * @return ProductImage
     */
    public function getBaseProductImage($baseProductId, $productImageId, array $relations = [], array $columns = ['*']);

    /**
     * Get product images for product.
     *
     * @param int   $productId
     * @param array $relations
     * @param array $columns
     *
     * @return Collection
     */
    public function getProductOnlyImages($productId, array $relations = [], array $columns = ['*']);

    /**
     * Get product images for product.
     *
     * @param int   $baseProductId
     * @param int   $productId
     * @param array $relations
     * @param array $columns
     *
     * @return Collection
     */
    public function getProductImages($baseProductId, $productId, array $relations = [], array $columns = ['*']);

    /**
     * Get product image for base product.
     *
     * @param int   $productId
     * @param int   $productImageId
     * @param array $relations
     * @param array $columns
     *
     * @return ProductImage
     */
    public function getProductImage($productId, $productImageId, array $relations = [], array $columns = ['*']);
}
