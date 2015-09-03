<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderStatusRuleRepository extends BaseRepository implements OrderStatusRuleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderStatusRule::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(OrderStatus $statusFrom, OrderStatus $statusTo)
    {
        return $this->create($this->idOf($statusFrom), $this->idOf($statusTo));
    }

    /**
     * @inheritdoc
     */
    public function create($statusIdFrom, $statusIdTo)
    {
        $resource = $this->createWith([], $this->getRelationships($statusIdFrom, $statusIdTo));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        OrderStatusRule $resource,
        OrderStatus $statusFrom = null,
        OrderStatus $statusTo = null
    ) {
        $this->update($resource, $this->idOf($statusFrom), $this->idOf($statusTo));
    }

    /**
     * @inheritdoc
     */
    public function update(OrderStatusRule $resource, $statusIdFrom = null, $statusIdTo = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($statusIdFrom, $statusIdTo));
    }

    /**
     * @param int $statusIdFrom
     * @param int $statusIdTo
     *
     * @return array
     */
    protected function getRelationships($statusIdFrom, $statusIdTo)
    {
        return $this->filterNulls([
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM => $statusIdFrom,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO   => $statusIdTo,
        ]);
    }
}
