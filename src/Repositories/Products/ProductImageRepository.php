<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductImage;
use \Illuminate\Database\Eloquent\Builder;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
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
    public function createWithObjects(BaseProduct $base, Image $image, array $attributes, Nullable $product = null)
    {
        return $this->create(
            $this->idOf($base),
            $this->idOf($image),
            $attributes,
            $this->idOfNullable($product, Product::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function create($baseProductId, $imageId, array $attributes, Nullable $productId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($baseProductId, $imageId, $productId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        ProductImage $resource,
        BaseProduct $base = null,
        Image $image = null,
        array $attributes = null,
        Nullable $product = null
    ) {
        $this->update(
            $resource,
            $this->idOf($base),
            $this->idOf($image),
            $attributes,
            $this->idOfNullable($product, Product::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function update(
        ProductImage $resource,
        $baseProductId = null,
        $imageId = null,
        array $attributes = null,
        Nullable $productId = null
    ) {
        $this->update($resource, $attributes, $this->getRelationships($baseProductId, $imageId, $productId));
    }

    /**
     * @inheritdoc
     */
    public function setAsCover(ProductImage $image)
    {
        /** @var BaseProduct $base */
        $base = $image->{ProductImage::FIELD_BASE_PRODUCT};

        $this->executeInTransaction(function () use ($base, $image) {
            // set all other images' isCover to false
            /** @noinspection PhpUndefinedMethodInspection */
            $base->productImages()->update([ProductImage::FIELD_IS_COVER => false]);

            // set isCover to true
            /** @noinspection PhpUndefinedMethodInspection */
            $base->productImages()
                ->where(ProductImage::FIELD_ID, '=', $image->{ProductImage::FIELD_ID})
                ->update([ProductImage::FIELD_IS_COVER => true]);

            // direct change of 'is_cover' is prohibited in model so we use this hack
            $rawAttributes = $image->getAttributes();
            $rawAttributes[ProductImage::FIELD_IS_COVER] = true;
            $image->setRawAttributes($rawAttributes);
        });
    }

    /**
     * @inheritdoc
     */
    public function getBaseProductImages($baseProductId, array $relations = [], array $columns = ['*'])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Builder $builder */
        $builder = $this
            ->getBuilder()
                ->with($relations)
                ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId)
                ->whereNull(ProductImage::FIELD_ID_PRODUCT);

        return $builder->get($columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductOnlyImages($productId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->getBuilder()
                ->with($relations)
                ->where(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $builder->get($columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductImages($baseProductId, $productId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->getBuilder()
            ->with($relations)
                ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId)
                ->orWhere(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $builder->get($columns);
    }

    /**
     * @inheritdoc
     */
    public function getBaseProductImage($baseProductId, $productImageId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->getBuilder()
            ->with($relations)
                ->where(ProductImage::FIELD_ID, '=', $productImageId)
                ->where(ProductImage::FIELD_ID_BASE_PRODUCT, '=', $baseProductId);

        return $builder->firstOrFail($columns);
    }

    /**
     * @inheritdoc
     */
    public function getProductImage($productId, $productImageId, array $relations = [], array $columns = ['*'])
    {
        $builder = $this
            ->getBuilder()
            ->with($relations)
                ->where(ProductImage::FIELD_ID, '=', $productImageId)
                ->where(ProductImage::FIELD_ID_PRODUCT, '=', $productId);

        return $builder->firstOrFail($columns);
    }

    /**
     * @param int           $baseProductId
     * @param int           $imageId
     * @param Nullable|null $productId
     *
     * @return array
     */
    protected function getRelationships($baseProductId, $imageId, Nullable $productId = null)
    {
        return $this->filterNulls([
            ProductImage::FIELD_ID_BASE_PRODUCT => $baseProductId,
            ProductImage::FIELD_ID_IMAGE        => $imageId,
        ], [
            ProductImage::FIELD_ID_PRODUCT      => $productId,
        ]);
    }

    /**
     * @return Builder
     */
    private function getBuilder()
    {
        return $this->getUnderlyingModel()->newQuery();
    }
}
