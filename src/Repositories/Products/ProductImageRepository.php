<?php namespace Neomerx\Core\Repositories\Products;

use \DB;
use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ProductImageRepository extends IndexBasedResourceRepository implements ProductImageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductImage::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $product, Image $image, array $attributes, Variant $variant = null)
    {
        /** @var ProductImage $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $product, $image, $attributes, $variant);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ProductImage $resource,
        Product $product = null,
        Image $image = null,
        array $attributes = null,
        Variant $variant = null
    ) {
        $this->fillModel($resource, [
            ProductImage::FIELD_ID_PRODUCT => $product,
            ProductImage::FIELD_ID_VARIANT => $variant,
            ProductImage::FIELD_ID_IMAGE   => $image,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function setAsCover(ProductImage $image)
    {
        /** @var Product $product */
        $product = $image->{ProductImage::FIELD_PRODUCT};

        DB::beginTransaction();
        try {

            // set all other images' isCover to false
            /** @noinspection PhpUndefinedMethodInspection */
            $product->productImages()
                ->update([ProductImage::FIELD_IS_COVER => false]);

            // set isCover to true
            /** @noinspection PhpUndefinedMethodInspection */
            $product->productImages()
                ->where(ProductImage::FIELD_ID, '=', $image->{ProductImage::FIELD_ID})
                ->update([ProductImage::FIELD_IS_COVER => true]);

            $allExecutedOk = true;

        } finally {
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }
}
