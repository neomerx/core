<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class OrderStatusRuleRepository extends IndexBasedResourceRepository implements OrderStatusRuleRepositoryInterface
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
    public function instance(OrderStatus $statusFrom, OrderStatus $statusTo)
    {
        /** @var OrderStatusRule $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $statusFrom, $statusTo);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(OrderStatusRule $resource, OrderStatus $statusFrom = null, OrderStatus $statusTo = null)
    {
        $this->fillModel($resource, [
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM => $statusFrom,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO   => $statusTo,
        ]);
    }
}
