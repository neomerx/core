<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ProductImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product      $product
     * @param Image        $image
     * @param array        $attributes
     * @param Variant|null $variant
     *
     * @return ProductImage
     */
    public function instance(Product $product, Image $image, array $attributes, Variant $variant = null);

    /**
     * @param ProductImage $resource
     * @param Product|null $product
     * @param Image|null   $image
     * @param array|null   $attributes
     * @param Variant|null $variant
     *
     * @return void
     */
    public function fill(
        ProductImage $resource,
        Product $product = null,
        Image $image = null,
        array $attributes = null,
        Variant $variant = null
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
