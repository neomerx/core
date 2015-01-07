<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Neomerx\Core\Api\Carriers\CarriersInterface;

/**
 * @see CarriersInterface
 *
 * @method static array  calculateTariffs(ShippingData $shippingData)
 * @method static Tariff calculateTariff(ShippingData $shippingData, Carrier $carrier)
 */
class Carriers extends Facade
{
    const INTERFACE_BIND_NAME = CarriersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
