<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Inventory\InventoryInterface;
use \Neomerx\Core\Models\Inventory as InventoryModel;

/**
 * @see InventoryInterface
 *
 * @method static InventoryModel read(Variant $variant, Warehouse $warehouse);
 * @method static void           increment(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void           makeReserve(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void           releaseReserve(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void decrement(Variant $variant, Warehouse $warehouse, int $quantity, bool $includingReserve = false);
 */
class Inventory extends Facade
{
    const INTERFACE_BIND_NAME = InventoryInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
