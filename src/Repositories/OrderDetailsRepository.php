<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;

class OrderDetailsRepository extends BaseRepository implements OrderDetailsRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderDetails::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Order $order,
        Variant $variant,
        ShippingOrder $shippingOrder = null,
        array $attributes = null
    ) {
        /** @var OrderDetails $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $variant, $shippingOrder, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        OrderDetails $details,
        Order $order = null,
        Variant $variant = null,
        ShippingOrder $shippingOrder = null,
        array $attributes = null
    ) {
        $this->fillModel($details, [
            OrderDetails::FIELD_ID_ORDER          => $order,
            OrderDetails::FIELD_ID_VARIANT        => $variant,
            OrderDetails::FIELD_ID_SHIPPING_ORDER => $shippingOrder,
        ], $attributes);
    }
}
