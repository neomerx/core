<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderHistory;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface OrderHistoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order       $order
     * @param OrderStatus $status
     *
     * @return OrderHistory
     *
     */
    public function instance(Order $order, OrderStatus $status);

    /**
     * @param OrderHistory     $resource
     * @param Order|null       $order
     * @param OrderStatus|null $status
     *
     * @return void
     *
     */
    public function fill(OrderHistory $resource, Order $order = null, OrderStatus $status = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return OrderHistory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
