<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ShippingStatusRepository extends CodeBasedResourceRepository implements ShippingStatusRepositoryInterface
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
    public function instance(array $attributes)
    {
        /** @var ShippingOrderStatus $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ShippingOrderStatus $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
