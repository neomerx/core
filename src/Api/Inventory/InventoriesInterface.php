<?php namespace Neomerx\Core\Api\Inventory;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;

interface InventoriesInterface
{
    const PARAM_WAREHOUSE_CODE = 'warehouse_code';

    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     * @param int       $quantity
     *
     * @return void
     */
    public function releaseReserve(Variant $variant, Warehouse $warehouse, $quantity);

    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     * @param int       $quantity
     *
     * @return void
     */
    public function makeReserve(Variant $variant, Warehouse $warehouse, $quantity);

    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     * @param int       $quantity
     *
     * @return void
     */
    public function increment(Variant $variant, Warehouse $warehouse, $quantity);

    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     * @param int       $quantity
     * @param bool      $includingReserve
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function decrement(Variant $variant, Warehouse $warehouse, $quantity, $includingReserve = false);

    /**
     * @param Variant   $variant
     * @param Warehouse $warehouse
     *
     * @return Inventory
     */
    public function read(Variant $variant, Warehouse $warehouse);
}
