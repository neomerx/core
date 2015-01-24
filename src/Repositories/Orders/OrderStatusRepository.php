<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class OrderStatusRepository extends CodeBasedResourceRepository implements OrderStatusRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderStatus::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var OrderStatus $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(OrderStatus $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
