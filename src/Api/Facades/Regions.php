<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Region;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Territories\RegionsInterface;

/**
 * @see RegionsInterface
 *
 * @method static Region     create(array $input)
 * @method static Region     read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection search(array $parameters = [])
 */
class Regions extends Facade
{
    const INTERFACE_BIND_NAME = RegionsInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
