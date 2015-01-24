<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ShippingOrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order               $order
     * @param Carrier             $carrier
     * @param ShippingOrderStatus $status
     * @param array               $attributes
     *
     * @return ShippingOrder
     */
    public function instance(Order $order, Carrier $carrier, ShippingOrderStatus $status, array $attributes);

    /**
     * @param ShippingOrder            $resource
     * @param Order|null               $order
     * @param Carrier|null             $carrier
     * @param ShippingOrderStatus|null $status
     * @param array|null               $attributes
     *
     * @return void
     */
    public function fill(
        ShippingOrder $resource,
        Order $order = null,
        Carrier $carrier = null,
        ShippingOrderStatus $status = null,
        array $attributes = null
    );

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ShippingOrder
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
