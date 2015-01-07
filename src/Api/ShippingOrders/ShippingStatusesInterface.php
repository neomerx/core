<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\ShippingOrderStatus;
use \Illuminate\Database\Eloquent\Collection;

interface ShippingStatusesInterface extends CrudInterface
{
    /**
     * Create shipping order status.
     *
     * @param array $input
     *
     * @return ShippingOrderStatus
     */
    public function create(array $input);

    /**
     * Read shipping order status by identifier.
     *
     * @param string $code
     *
     * @return ShippingOrderStatus
     */
    public function read($code);

    /**
     * Get all available shipping statuses.
     *
     * @return Collection
     */
    public function all();
}
