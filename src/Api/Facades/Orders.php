<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Order;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Orders\OrdersInterface;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @see OrdersInterface
 *
 * @method static Order      create(array $input)
 * @method static Order      read(int $id)
 * @method static void       update(int $id, array $input)
 * @method static void       delete(int $id)
 * @method static Collection search(array $parameters = [])
 */
class Orders extends Facade
{
    const INTERFACE_BIND_NAME = OrdersInterface::class;
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
