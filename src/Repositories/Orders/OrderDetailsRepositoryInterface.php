<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderDetailsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order         $order
     * @param Product       $product
     * @param array         $attributes
     * @param Nullable|null $shippingOrder ShippingOrder
     *
     * @return OrderDetails
     */
    public function createWithObjects(
        Order $order,
        Product $product,
        array $attributes,
        Nullable $shippingOrder = null
    );

    /**
     * @param int           $orderId
     * @param int           $productId
     * @param array         $attributes
     * @param Nullable|null $shippingOrderId
     *
     * @return OrderDetails
     */
    public function create(
        $orderId,
        $productId,
        array $attributes,
        Nullable $shippingOrderId = null
    );

    /**
     * @param OrderDetails  $details
     * @param Order|null    $order
     * @param Product|null  $product
     * @param array|null    $attributes
     * @param Nullable|null $shippingOrder ShippingOrder
     *
     * @return void
     */
    public function updateWithObjects(
        OrderDetails $details,
        Order $order = null,
        Product $product = null,
        array $attributes = null,
        Nullable $shippingOrder = null
    );

    /**
     * @param OrderDetails  $details
     * @param int|null      $orderId
     * @param int|null      $productId
     * @param array|null    $attributes
     * @param Nullable|null $shippingOrderId
     *
     * @return void
     */
    public function update(
        OrderDetails $details,
        $orderId = null,
        $productId = null,
        array $attributes = null,
        Nullable $shippingOrderId = null
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
