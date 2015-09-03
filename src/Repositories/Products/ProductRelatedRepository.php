<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\ProductRelated;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductRelatedRepository extends BaseRepository implements ProductRelatedRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ProductRelated::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Product $product, Product $related)
    {
        return $this->create($this->idOf($product), $this->idOf($related));
    }

    /**
     * @inheritdoc
     */
    public function create($productId, $relatedId)
    {
        $resource = $this->createWith([], $this->getRelationships($productId, $relatedId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(ProductRelated $resource, Product $product = null, Product $related = null)
    {
        $this->update($resource, $this->idOf($product), $this->idOf($related));
    }

    /**
     * @inheritdoc
     */
    public function update(ProductRelated $resource, $productId = null, $relatedId = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($productId, $relatedId));
    }

    /**
     * @param int $productId
     * @param int $relatedId
     *
     * @return array
     */
    protected function getRelationships($productId, $relatedId)
    {
        return $this->filterNulls([
            ProductRelated::FIELD_ID_PRODUCT         => $productId,
            ProductRelated::FIELD_ID_RELATED_PRODUCT => $relatedId,
        ]);
    }
}
