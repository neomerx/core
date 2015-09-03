<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface InventoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product   $product
     * @param Warehouse $warehouse
     * @param array     $attributes
     *
     * @return Inventory
     */
    public function createWithObjects(Product $product, Warehouse $warehouse, array $attributes);

    /**
     * @param int   $productId
     * @param int   $warehouseId
     * @param array $attributes
     *
     * @return Inventory
     */
    public function create($productId, $warehouseId, array $attributes);

    /**
     * @param Inventory      $resource
     * @param Product|null   $product
     * @param Warehouse|null $warehouse
     * @param array|null     $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        Inventory $resource,
        Product $product = null,
        Warehouse $warehouse = null,
        array $attributes = null
    );

    /**
     * @param Inventory  $resource
     * @param int|null   $productId
     * @param int|null   $warehouseId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(
        Inventory $resource,
        $productId = null,
        $warehouseId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Inventory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
