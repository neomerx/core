<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class SupplyOrderDetailsRepository extends IndexBasedResourceRepository implements SupplyOrderDetailsRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplyOrderDetails::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(SupplyOrder $order, Variant $variant, array $attributes)
    {
        /** @var SupplyOrderDetails $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $variant, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        SupplyOrderDetails $resource,
        SupplyOrder $order = null,
        Variant $variant = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            SupplyOrderDetails::FIELD_ID_SUPPLY_ORDER => $order,
            SupplyOrderDetails::FIELD_ID_VARIANT      => $variant,
        ], $attributes);
    }
}
