<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ShippingOrderRepository extends IndexBasedResourceRepository implements ShippingOrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ShippingOrder::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Carrier $carrier, ShippingOrderStatus $status, array $attributes)
    {
        /** @var ShippingOrder $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $carrier, $status, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ShippingOrder $resource,
        Carrier $carrier = null,
        ShippingOrderStatus $status = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            ShippingOrder::FIELD_ID_CARRIER               => $carrier,
            ShippingOrder::FIELD_ID_SHIPPING_ORDER_STATUS => $status,
        ], $attributes);
    }
}
