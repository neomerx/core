<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;

class OrderDetailsRepository extends IndexBasedResourceRepository implements OrderDetailsRepositoryInterface
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
        array $attributes,
        ShippingOrder $shippingOrder = null
    ) {
        /** @var OrderDetails $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $variant, $attributes, $shippingOrder);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        OrderDetails $details,
        Order $order = null,
        Variant $variant = null,
        array $attributes = null,
        ShippingOrder $shippingOrder = null
    ) {
        $this->fillModel($details, [
            OrderDetails::FIELD_ID_ORDER          => $order,
            OrderDetails::FIELD_ID_VARIANT        => $variant,
            OrderDetails::FIELD_ID_SHIPPING_ORDER => $shippingOrder,
        ], $attributes);
    }
}
