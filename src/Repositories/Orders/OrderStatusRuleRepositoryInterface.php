<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderStatusRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param OrderStatus $statusFrom
     * @param OrderStatus $statusTo
     *
     * @return OrderStatusRule
     *
     */
    public function createWithObjects(OrderStatus $statusFrom, OrderStatus $statusTo);

    /**
     * @param int $statusIdFrom
     * @param int $statusIdTo
     *
     * @return OrderStatusRule
     *
     */
    public function create($statusIdFrom, $statusIdTo);

    /**
     * @param OrderStatusRule  $resource
     * @param OrderStatus|null $statusFrom
     * @param OrderStatus|null $statusTo
     *
     * @return void
     *
     */
    public function updateWithObjects(
        OrderStatusRule $resource,
        OrderStatus $statusFrom = null,
        OrderStatus $statusTo = null
    );

    /**
     * @param OrderStatusRule $resource
     * @param int|null        $statusIdFrom
     * @param int|null        $statusIdTo
     *
     * @return void
     *
     */
    public function update(OrderStatusRule $resource, $statusIdFrom = null, $statusIdTo = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return OrderStatusRule
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
