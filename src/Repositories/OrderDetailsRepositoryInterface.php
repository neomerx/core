<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;

interface OrderDetailsRepositoryInterface
{
    /**
     * @param Order              $order
     * @param Variant            $variant
     * @param ShippingOrder|null $shippingOrder
     * @param array|null         $attributes
     *
     * @return OrderDetails
     */
    public function instance(
        Order $order,
        Variant $variant,
        ShippingOrder $shippingOrder = null,
        array $attributes = null
    );

    /**
     * @param OrderDetails       $details
     * @param Order|null         $order
     * @param Variant|null       $variant
     * @param ShippingOrder|null $shippingOrder
     * @param array|null         $attributes
     *
     * @return void
     */
    public function fill(
        OrderDetails $details,
        Order $order = null,
        Variant $variant = null,
        ShippingOrder $shippingOrder = null,
        array $attributes = null
    );
}
