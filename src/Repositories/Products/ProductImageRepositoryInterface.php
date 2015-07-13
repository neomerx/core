<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductImage;
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
}
