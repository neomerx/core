<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Supplier;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Suppliers\SuppliersInterface;

/**
 * @see SuppliersInterface
 *
 * @method static Supplier   create(array $input)
 * @method static Supplier   read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection search(array $parameters = [])
 */
class Suppliers extends Facade
{
    const INTERFACE_BIND_NAME = SuppliersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
