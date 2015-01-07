<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Carrier;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ConfigurationException;

class Carriers implements CarriersInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @var Carrier
     */
    private $carrierModel;

    /**
     * @param Carrier $carrier
     */
    public function __construct(Carrier $carrier)
    {
        $this->carrierModel = $carrier;
    }

    /**
     * Select all carriers matching $shippingData and calculate their tariffs.
     *
     * @param ShippingData $shippingData
     *
     * @return array [..., [Carrier, Tariff], ...]
     */
    public function calculateTariffs(ShippingData $shippingData)
    {
        $tariffs = [];

        /** @var Carrier $carrier */
        foreach ($this->selectCarriers($shippingData) as $carrier) {
            $tariffs[] = [$carrier, $this->calculateTariff($shippingData, $carrier)];
        }

        return $tariffs;
    }

    /**
     * Calculate tariff for specified carrier.
     *
     * @param ShippingData $shippingData
     * @param Carrier      $carrier
     *
     * @return Tariff
     */
    public function calculateTariff(ShippingData $shippingData, Carrier $carrier)
    {
        /** @var TariffCalculatorInterface $calculator */
        /** @noinspection PhpUndefinedMethodInspection */
        $calculator = App::make($carrier->factory);

        $isCalculator  = $calculator instanceof TariffCalculatorInterface;
        $isCalculator ?: S\throwEx(new ConfigurationException(Carrier::FIELD_FACTORY));

        $calculator->init($carrier);
        return $calculator->calculate(TariffCalculatorData::newFromShippingData($carrier->data, $shippingData));
    }

    /**
     * @param ShippingData $shippingData
     *
     * @return Collection
     */
    private function selectCarriers(ShippingData $shippingData)
    {
        $region = $shippingData->getAddressTo()->region;

        return $this->carrierModel->selectCarriers(
            $region->id_country,
            $region->id_region,
            $shippingData->getAddressTo()->postcode,
            $shippingData->getCustomer()->id_customer_type,
            $shippingData->getCart()->getWeight(),
            $shippingData->getCart()->getMaxDimension(),
            $shippingData->getCart()->getPriceWoTax()
        );
    }
}
