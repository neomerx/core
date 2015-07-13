<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderDetailsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order              $order
     * @param Product            $product
     * @param array              $attributes
     * @param ShippingOrder|null $shippingOrder
     *
     * @return OrderDetails
     */
    public function instance(
        Order $order,
        Product $product,
        array $attributes,
        ShippingOrder $shippingOrder = null
    );

    /**
     * @param OrderDetails       $details
     * @param Order|null         $order
     * @param Product|null       $product
     * @param array|null         $attributes
     * @param ShippingOrder|null $shippingOrder
     *
     * @return void
     */
    public function fill(
        OrderDetails $details,
        Order $order = null,
        Product $product = null,
        array $attributes = null,
        ShippingOrder $shippingOrder = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return OrderDetails
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
