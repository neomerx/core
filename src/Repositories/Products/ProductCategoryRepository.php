<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductCategory::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Product $product, Category $category, array $attributes)
    {
        $resource = $this->create($this->idOf($product), $this->idOf($category), $attributes);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function create($productId, $categoryId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($productId, $categoryId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        ProductCategory $resource,
        Product $product = null,
        Category $category = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($product), $this->idOf($category), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ProductCategory $resource,
        $productId = null,
        $categoryId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($productId, $categoryId));
    }

    /**
     * @param int $productId
     * @param int $categoryId
     *
     * @return array
     */
    private function getRelationships($productId, $categoryId)
    {
        return $this->filterNulls([
            ProductCategory::FIELD_ID_PRODUCT  => $productId,
            ProductCategory::FIELD_ID_CATEGORY => $categoryId,
        ]);
    }
}
