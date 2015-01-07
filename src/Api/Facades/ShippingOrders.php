<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Models\ShippingOrder;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\ShippingOrders\ShippingOrdersInterface;

/**
 * @see ShippingOrdersInterface
 *
 * @method static ShippingOrder create(array $input)
 * @method static ShippingOrder read(int $id)
 * @method static void          update(int $id, array $input)
 * @method static void          delete(int $id)
 * @method static Collection    search(array $parameters = [])
 * @method static Tariff        calculateCosts(ShippingData $shippingData, Carrier $carrier)
 */
class ShippingOrders extends Facade
{
    const INTERFACE_BIND_NAME = ShippingOrdersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
