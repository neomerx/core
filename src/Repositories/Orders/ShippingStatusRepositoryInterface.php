<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ShippingStatusRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return ShippingOrderStatus
     */
    public function create(array $attributes);

    /**
     * @param ShippingOrderStatus $resource
     * @param array               $attributes
     *
     * @return void
     */
    public function update(ShippingOrderStatus $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ShippingOrderStatus
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
