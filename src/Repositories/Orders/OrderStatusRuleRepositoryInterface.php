<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface OrderStatusRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param OrderStatus $statusFrom
     * @param OrderStatus $statusTo
     *
     * @return OrderStatusRule
     *
     */
    public function instance(OrderStatus $statusFrom, OrderStatus $statusTo);

    /**
     * @param OrderStatusRule  $resource
     * @param OrderStatus|null $statusFrom
     * @param OrderStatus|null $statusTo
     *
     * @return void
     *
     */
    public function fill(OrderStatusRule $resource, OrderStatus $statusFrom = null, OrderStatus $statusTo = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return OrderStatusRule
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
