<?php namespace Neomerx\Core\Api\Inventory;

use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Models\Inventory as InventoryModel;
use \Neomerx\Core\Models\Warehouse as WarehouseModel;

interface InventoryInterface
{
    const PARAM_WAREHOUSE_CODE = 'warehouse_code';

    /**
     * @param VariantModel   $variant
     * @param WarehouseModel $warehouse
     * @param int            $quantity
     *
     * @return void
     */
    public function releaseReserve(VariantModel $variant, WarehouseModel $warehouse, $quantity);

    /**
     * @param VariantModel   $variant
     * @param WarehouseModel $warehouse
     * @param int            $quantity
     *
     * @return void
     */
    public function makeReserve(VariantModel $variant, WarehouseModel $warehouse, $quantity);

    /**
     * @param VariantModel   $variant
     * @param WarehouseModel $warehouse
     * @param int            $quantity
     *
     * @return void
     */
    public function increment(VariantModel $variant, WarehouseModel $warehouse, $quantity);

    /**
     * @param VariantModel   $variant
     * @param WarehouseModel $warehouse
     * @param int            $quantity
     * @param bool           $includingReserve
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function decrement(VariantModel $variant, WarehouseModel $warehouse, $quantity, $includingReserve = false);

    /**
     * @param VariantModel   $variant
     * @param WarehouseModel $warehouse
     *
     * @return InventoryModel
     */
    public function read(VariantModel $variant, WarehouseModel $warehouse);
}
