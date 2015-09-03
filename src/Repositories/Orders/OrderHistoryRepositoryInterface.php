<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderHistory;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderHistoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order       $order
     * @param OrderStatus $status
     *
     * @return OrderHistory
     *
     */
    public function createWithObjects(Order $order, OrderStatus $status);

    /**
     * @param int $orderId
     * @param int $statusId
     *
     * @return OrderHistory
     *
     */
    public function create($orderId, $statusId);

    /**
     * @param OrderHistory     $resource
     * @param Order|null       $order
     * @param OrderStatus|null $status
     *
     * @return void
     *
     */
    public function updateWithObjects(OrderHistory $resource, Order $order = null, OrderStatus $status = null);

    /**
     * @param OrderHistory $resource
     * @param int|null     $orderId
     * @param int|null     $statusId
     *
     * @return void
     *
     */
    public function update(OrderHistory $resource, $orderId = null, $statusId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return OrderHistory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
