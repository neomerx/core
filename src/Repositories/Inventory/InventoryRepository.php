<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class InventoryRepository extends IndexBasedResourceRepository implements InventoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Inventory::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Variant $variant, Warehouse $warehouse, array $attributes)
    {
        /** @var Inventory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $variant, $warehouse, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Inventory $resource,
        Variant $variant = null,
        Warehouse $warehouse = null,
        array $attributes = null
    ) {
        $attributes = isset($attributes) === true ? $attributes : [];
        $attributes[Inventory::FIELD_SKU] = $variant->{Variant::FIELD_SKU};
        $this->fillModel($resource, [
            Inventory::FIELD_ID_WAREHOUSE => $warehouse,
        ], $attributes);
    }
}
