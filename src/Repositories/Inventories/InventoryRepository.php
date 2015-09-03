<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
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
    public function createWithObjects(Product $product, Warehouse $warehouse, array $attributes)
    {
        return $this->create($this->idOf($product), $this->idOf($warehouse), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($productId, $warehouseId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($productId, $warehouseId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        Inventory $resource,
        Product $product = null,
        Warehouse $warehouse = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($product), $this->idOf($warehouse), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        Inventory $resource,
        $productId = null,
        $warehouseId = null,
        array $attributes = null
    ) {
        $this->update($resource, $attributes, $this->getRelationships($productId, $warehouseId));
    }

    /**
     * @param int $productId
     * @param int $warehouseId
     *
     * @return array
     */
    protected function getRelationships($productId, $warehouseId)
    {
        return $this->filterNulls([
            Inventory::FIELD_ID_PRODUCT   => $productId,
            Inventory::FIELD_ID_WAREHOUSE => $warehouseId,
        ]);
    }
}
