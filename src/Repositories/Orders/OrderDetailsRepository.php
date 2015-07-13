<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class OrderDetailsRepository extends IndexBasedResourceRepository implements OrderDetailsRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderDetails::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Order $order,
        Product $product,
        array $attributes,
        ShippingOrder $shippingOrder = null
    ) {
        /** @var OrderDetails $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $product, $attributes, $shippingOrder);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        OrderDetails $details,
        Order $order = null,
        Product $product = null,
        array $attributes = null,
        ShippingOrder $shippingOrder = null
    ) {
        $this->fillModel($details, [
            OrderDetails::FIELD_ID_ORDER          => $order,
            OrderDetails::FIELD_ID_PRODUCT        => $product,
            OrderDetails::FIELD_ID_SHIPPING_ORDER => $shippingOrder,
        ], $attributes);
    }
}
