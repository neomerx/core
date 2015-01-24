<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface ShippingStatusRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return ShippingOrderStatus
     */
    public function instance(array $attributes);

    /**
     * @param ShippingOrderStatus $resource
     * @param array               $attributes
     *
     * @return void
     */
    public function fill(ShippingOrderStatus $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return ShippingOrderStatus
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
