<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
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
     * @param BaseProduct  $base
     * @param Image        $image
     * @param array        $attributes
     * @param Product|null $product
     *
     * @return ProductImage
     */
    public function instance(BaseProduct $base, Image $image, array $attributes, Product $product = null);

    /**
     * @param BaseProduct $base
     * @param Image       $image
     * @param array       $attributes
     *
     * @return ProductImage
     */
    public function instanceForBaseProduct(BaseProduct $base, Image $image, array $attributes);

    /**
     * @param Product $product
     * @param Image   $image
     * @param array   $attributes
     *
     * @return ProductImage
     */
    public function instanceForProduct(Product $product, Image $image, array $attributes);

    /**
     * @param ProductImage     $resource
     * @param BaseProduct|null $base
     * @param Image|null       $image
     * @param array|null       $attributes
     * @param Product|null     $product
     *
     * @return void
     */
    public function fill(
        ProductImage $resource,
        BaseProduct $base = null,
        Image $image = null,
        array $attributes = null,
        Product $product = null
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
