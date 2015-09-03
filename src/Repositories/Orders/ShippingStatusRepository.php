<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ShippingStatusRepository extends BaseRepository implements ShippingStatusRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ShippingOrderStatus::class);
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
    public function update(ShippingOrderStatus $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
