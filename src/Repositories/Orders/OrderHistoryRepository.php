<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderHistory;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderHistoryRepository extends BaseRepository implements OrderHistoryRepositoryInterface
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
    public function createWithObjects(Order $order, OrderStatus $status)
    {
        return $this->create($this->idOf($order), $this->idOf($status));
    }

    /**
     * @inheritdoc
     */
    public function create($orderId, $statusId)
    {
        $resource = $this->createWith([], $this->getRelationships($orderId, $statusId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(OrderHistory $resource, Order $order = null, OrderStatus $status = null)
    {
        $this->update($resource, $this->idOf($order), $this->idOf($status));
    }

    /**
     * @inheritdoc
     */
    public function update(OrderHistory $resource, $orderId = null, $statusId = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($orderId, $statusId));
    }

    /**
     * @param int $orderId
     * @param int $statusId
     *
     * @return array
     */
    private function getRelationships($orderId, $statusId)
    {
        return $this->filterNulls([
            OrderHistory::FIELD_ID_ORDER        => $orderId,
            OrderHistory::FIELD_ID_ORDER_STATUS => $statusId,
        ]);
    }
}
