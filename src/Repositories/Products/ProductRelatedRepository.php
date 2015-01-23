<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\ProductRelated;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ProductRelatedRepository extends IndexBasedResourceRepository implements ProductRelatedRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductRelated::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $product, Product $related)
    {
        /** @var ProductRelated $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $product, $related);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ProductRelated $resource, Product $product = null, Product $related = null)
    {
        $this->fillModel($resource, [
            ProductRelated::FIELD_ID_PRODUCT         => $product,
            ProductRelated::FIELD_ID_RELATED_PRODUCT => $related,
        ], []);
    }
}
