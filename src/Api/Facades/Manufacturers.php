<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Manufacturer;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Manufacturers\ManufacturersInterface;

/**
 * @see ManufacturersInterface
 *
 * @method static Manufacturer create(array $input)
 * @method static Manufacturer read(string $code)
 * @method static void         update(string $code, array $input)
 * @method static void         delete(string $code)
 * @method static Collection   search(array $parameters = [])
 */
class Manufacturers extends Facade
{
    const INTERFACE_BIND_NAME = ManufacturersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
