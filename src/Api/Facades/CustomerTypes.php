<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\CustomerType;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Customers\CustomerTypesInterface;

/**
 * @see CustomerTypesInterface
 *
 * @method static CustomerType create(array $input)
 * @method static CustomerType read(string $code)
 * @method static void         update(string $code, array $input)
 * @method static void         delete(string $code)
 * @method static Collection   all()
 */
final class CustomerTypes extends Facade
{
    const INTERFACE_BIND_NAME = CustomerTypesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
