<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Warehouses\WarehousesInterface;

/**
 * @see WarehousesInterface
 *
 * @method static Warehouse getDefault()
 */
class Warehouses extends Facade
{
    const INTERFACE_BIND_NAME = WarehousesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
