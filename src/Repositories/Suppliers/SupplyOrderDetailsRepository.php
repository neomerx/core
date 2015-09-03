<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class SupplyOrderDetailsRepository extends BaseRepository implements SupplyOrderDetailsRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplyOrderDetails::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(SupplyOrder $order, Product $product, array $attributes)
    {
        return $this->create($this->idOf($order), $this->idOf($product), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($orderId, $productId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($orderId, $productId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        SupplyOrderDetails $resource,
        SupplyOrder $order = null,
        Product $product = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($order), $this->idOf($product), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        SupplyOrderDetails $resource,
        $orderId = null,
        $productId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($orderId, $productId));
    }

    /**
     * @param int $orderId
     * @param int $productId
     *
     * @return array
     */
    protected function getRelationships($orderId, $productId)
    {
        return $this->filterNulls([
            SupplyOrderDetails::FIELD_ID_SUPPLY_ORDER => $orderId,
            SupplyOrderDetails::FIELD_ID_PRODUCT      => $productId,
        ]);
    }
}
