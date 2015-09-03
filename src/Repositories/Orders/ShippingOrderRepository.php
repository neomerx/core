<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ShippingOrderRepository extends BaseRepository implements ShippingOrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ShippingOrder::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Carrier $carrier, ShippingOrderStatus $status, array $attributes)
    {
        return $this->create($this->idOf($carrier), $this->idOf($status), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($carrierId, $statusId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($carrierId, $statusId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        ShippingOrder $resource,
        Carrier $carrier = null,
        ShippingOrderStatus $status = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($carrier), $this->idOf($status), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        ShippingOrder $resource,
        $carrierId = null,
        $statusId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($carrierId, $statusId));
    }

    /**
     * @param int $carrierId
     * @param int $statusId
     *
     * @return array
     */
    protected function getRelationships($carrierId, $statusId)
    {
        return $this->filterNulls([
            ShippingOrder::FIELD_ID_CARRIER               => $carrierId,
            ShippingOrder::FIELD_ID_SHIPPING_ORDER_STATUS => $statusId,
        ]);
    }
}
