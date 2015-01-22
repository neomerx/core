<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ProductCategoryRepository extends IndexBasedResourceRepository implements ProductCategoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductCategory::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $product, Category $category, array $attributes)
    {
        /** @var ProductCategory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $product, $category, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ProductCategory $resource,
        Product $product = null,
        Category $category = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            ProductCategory::FIELD_ID_PRODUCT  => $product,
            ProductCategory::FIELD_ID_CATEGORY => $category,
        ], $attributes);
    }
}
