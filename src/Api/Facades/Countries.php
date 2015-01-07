<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Country;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Territories\CountriesInterface;

/**
 * @see CountriesInterface
 *
 * @method static Country    create(array $input)
 * @method static Country    read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection all()
 * @method static Collection regions(string $code)
 */
class Countries extends Facade
{
    const INTERFACE_BIND_NAME = CountriesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
