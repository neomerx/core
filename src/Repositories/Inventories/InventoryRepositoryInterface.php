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
    public function instance(Product $product, Warehouse $warehouse, array $attributes);

    /**
     * @param Inventory      $resource
     * @param Product|null   $product
     * @param Warehouse|null $warehouse
     * @param array|null     $attributes
     *
     * @return void
     */
    public function fill(
        Inventory $resource,
        Product $product = null,
        Warehouse $warehouse = null,
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
