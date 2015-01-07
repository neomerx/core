<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Models\Inventory;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Inventory\InventoriesInterface;

/**
 * @see InventoriesInterface
 *
 * @method static Inventory read(Variant $variant, Warehouse $warehouse);
 * @method static void      increment(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void      makeReserve(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void      releaseReserve(Variant $variant, Warehouse $warehouse, int $quantity);
 * @method static void decrement(Variant $variant, Warehouse $warehouse, int $quantity, bool $includingReserve = false);
 */
class Inventories extends Facade
{
    const INTERFACE_BIND_NAME = InventoriesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
