<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Customer;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Customers\CustomersInterface;

/**
 * @see CustomersInterface
 *
 * @method static Customer   create(array $input)
 * @method static Customer   read(int $customerId)
 * @method static void       update(int $customerId, array $input)
 * @method static void       delete(int $customerId)
 * @method static Collection search(array $parameters = [])
 */
final class Customers extends Facade
{
    const INTERFACE_BIND_NAME = CustomersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
