<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderStatusRepository extends BaseRepository implements OrderStatusRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderStatus::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(OrderStatus $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
