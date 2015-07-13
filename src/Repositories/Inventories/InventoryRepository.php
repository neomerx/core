<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class InventoryRepository extends IndexBasedResourceRepository implements InventoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Inventory::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $product, Warehouse $warehouse, array $attributes)
    {
        /** @var Inventory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $product, $warehouse, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Inventory $resource,
        Product $product = null,
        Warehouse $warehouse = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            Inventory::FIELD_ID_PRODUCT   => $product,
            Inventory::FIELD_ID_WAREHOUSE => $warehouse,
        ], $attributes);
    }
}
