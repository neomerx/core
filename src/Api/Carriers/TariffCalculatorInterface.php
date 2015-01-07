<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Models\Carrier;

interface TariffCalculatorInterface
{
    public function init(Carrier $carrier);

    /**
     * @param TariffCalculatorData $data
     *
     * @return Tariff
     */
    public function calculate(TariffCalculatorData $data);
}
