<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Api\Cart\Cart;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;

class TariffCalculatorData extends ShippingData
{
    /**
     * @var string
     */
    private $carrierData;

    /**
     * @param string   $carrierData
     * @param Customer $customer
     * @param Cart     $cart
     * @param Address  $addressTo
     * @param Address  $addressFrom
     */
    public function __construct(
        $carrierData,
        Customer $customer,
        Cart $cart,
        Address $addressTo,
        Address $addressFrom = null
    ) {
        parent::__construct($customer, $cart, $addressTo, null, $addressFrom);
        $this->carrierData = $carrierData;
    }

    /**
     * @return string
     */
    public function getCarrierData()
    {
        return $this->carrierData;
    }

    /**
     * @param string       $carrierData
     * @param ShippingData $shippingData
     *
     * @return TariffCalculatorData
     */
    public static function newFromShippingData($carrierData, ShippingData $shippingData)
    {
        return new self(
            $carrierData,
            $shippingData->getCustomer(),
            $shippingData->getCart(),
            $shippingData->getAddressTo(),
            $shippingData->getAddressFrom()
        );
    }
}
