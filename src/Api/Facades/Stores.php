<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Store;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Stores\StoresInterface;

/**
 * @see StoresInterface
 *
 * @method static Store getDefault()
 */
class Stores extends Facade
{
    const INTERFACE_BIND_NAME = StoresInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
