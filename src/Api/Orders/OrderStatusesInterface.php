<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\OrderStatus;
use \Illuminate\Database\Eloquent\Collection;

interface OrderStatusesInterface extends CrudInterface
{
    const PARAM_RULE_CODES         = 'available_status_codes';
    const PARAM_AVAILABLE_STATUSES = OrderStatus::FIELD_AVAILABLE_STATUSES;

    /**
     * Create order status.
     *
     * @param array $input
     *
     * @return OrderStatus
     */
    public function create(array $input);

    /**
     * Read order status by identifier.
     *
     * @param string $code
     *
     * @return OrderStatus
     */
    public function read($code);

    /**
     * Get all available orders statuses.
     *
     * @return Collection
     */
    public function all();

    /**
     * Add a new available status for $codeFrom.
     *
     * @param OrderStatus $statusFrom
     * @param OrderStatus $statusTo
     *
     * @return void
     */
    public function addAvailable(OrderStatus $statusFrom, OrderStatus $statusTo);

    /**
     * Remove available status from $codeFrom.
     *
     * @param OrderStatus $statusFrom
     * @param OrderStatus $statusTo
     *
     * @return void
     */
    public function removeAvailable(OrderStatus $statusFrom, OrderStatus $statusTo);
}
