<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderHistory;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class OrderHistoryRepository extends IndexBasedResourceRepository implements OrderHistoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderHistory::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Order $order, OrderStatus $status)
    {
        /** @var OrderHistory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $status);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(OrderHistory $resource, Order $order = null, OrderStatus $status = null)
    {
        $this->fillModel($resource, [
            OrderHistory::FIELD_ID_ORDER        => $order,
            OrderHistory::FIELD_ID_ORDER_STATUS => $status,
        ]);
    }
}
