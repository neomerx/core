<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Models\Carrier;

interface CarriersInterface
{
    const PARAM_PROPERTIES = Carrier::FIELD_PROPERTIES;

    /**
     * @param ShippingData $shippingData
     * @param Carrier      $carrier
     *
     * @return Tariff
     */
    public function calculateTariff(ShippingData $shippingData, Carrier $carrier);

    /**
     * @param ShippingData $shippingData
     *
     * @return array
     */
    public function calculateTariffs(ShippingData $shippingData);
}
