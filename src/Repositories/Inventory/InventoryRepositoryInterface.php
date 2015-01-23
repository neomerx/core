<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface InventoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     * @param array     $attributes
     *
     * @return Inventory
     */
    public function instance(Variant $variant, Warehouse $warehouse, array $attributes);

    /**
     * @param Inventory      $resource
     * @param Variant|null   $variant
     * @param Warehouse|null $warehouse
     * @param array|null     $attributes
     *
     */
    public function fill(
        Inventory $resource,
        Variant $variant = null,
        Warehouse $warehouse = null,
        array $attributes = null
    );

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Inventory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
