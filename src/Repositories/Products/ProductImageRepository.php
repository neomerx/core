<?php namespace Neomerx\Core\Repositories\Products;

use \DB;
use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ProductImageRepository extends IndexBasedResourceRepository implements ProductImageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductImage::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(BaseProduct $base, Image $image, array $attributes, Product $product = null)
    {
        /** @var ProductImage $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $base, $image, $attributes, $product);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ProductImage $resource,
        BaseProduct $base = null,
        Image $image = null,
        array $attributes = null,
        Product $product = null
    ) {
        $this->fillModel($resource, [
            ProductImage::FIELD_ID_BASE_PRODUCT => $base,
            ProductImage::FIELD_ID_PRODUCT      => $product,
            ProductImage::FIELD_ID_IMAGE        => $image,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function setAsCover(ProductImage $image)
    {
        /** @var BaseProduct $base */
        $base = $image->{ProductImage::FIELD_BASE_PRODUCT};

        DB::beginTransaction();
        try {
            // set all other images' isCover to false
            /** @noinspection PhpUndefinedMethodInspection */
            $base->productImages()->update([ProductImage::FIELD_IS_COVER => false]);

            // set isCover to true
            /** @noinspection PhpUndefinedMethodInspection */
            $base->productImages()
                ->where(ProductImage::FIELD_ID, '=', $image->{ProductImage::FIELD_ID})
                ->update([ProductImage::FIELD_IS_COVER => true]);

            $allExecutedOk = true;
        } finally {
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @inheritdoc
     */
    public function getBaseProductImages($baseProductId, array $relations = [], array $columns = ['*'])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this
            ->createBuilder($relations)
            ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId)
            ->whereNull(ProductImage::FIELD_ID_PRODUCT);

        return $this->executeGet($builder, $columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductOnlyImages($productId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->createBuilder($relations)
            ->where(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $this->executeGet($builder, $columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductImages($baseProductId, $productId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->createBuilder($relations)
            ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId)
            ->orWhere(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $this->executeGet($builder, $columns);
    }

    /**
     * @inheritdoc
     */
    public function getBaseProductImage($baseProductId, $productImageId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->createBuilder($relations)
            ->where(ProductImage::FIELD_ID, '=', $productImageId)
            ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId);

        return $this->executeFirstOrFail($builder, $columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductImage($productId, $productImageId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->createBuilder($relations)
            ->where(ProductImage::FIELD_ID, '=', $productImageId)
            ->where(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $this->executeFirstOrFail($builder, $columns);
    }
}
