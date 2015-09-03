<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderStatusRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return OrderStatus
     */
    public function create(array $attributes);

    /**
     * @param OrderStatus $resource
     * @param array       $attributes
     *
     * @return void
     */
    public function update(OrderStatus $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return OrderStatus
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
