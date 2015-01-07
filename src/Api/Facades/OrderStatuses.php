<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\OrderStatus;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Orders\OrderStatusesInterface;

/**
 * @see OrderStatusesInterface
 *
 * @method static OrderStatus create(array $input)
 * @method static OrderStatus read(string $code)
 * @method static void        update(string $code, array $input)
 * @method static void        delete(string $code)
 * @method static Collection  all()
 * @method static void        addAvailable(OrderStatus $statusFrom, OrderStatus $statusTo)
 * @method static void        removeAvailable(OrderStatus $statusFrom, OrderStatus $statusTo)
 */
class OrderStatuses extends Facade
{
    const INTERFACE_BIND_NAME = OrderStatusesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
