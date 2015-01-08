<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Customer;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Customers\CustomersInterface as Api;

class CustomerConverterWithAddress extends CustomerConverterGeneric
{
    const BIND_NAME = __CLASS__;

    /**
     * @var ConverterInterface
     */
    private $addressConverter;

    /**
     * @param ConverterInterface $converter Address converter
     */
    public function __construct(ConverterInterface $converter = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->addressConverter = $converter ? $converter : App::make(AddressConverterGeneric::BIND_NAME);
    }

    /**
     * Format model to array representation.
     *
     * @param Customer $customer
     *
     * @return null|array<mixed,mixed>
     */
    public function convert($customer = null)
    {
        if ($customer === null) {
            return null;
        }

        $result = parent::convert($customer);

        /** @var Collection $billingAddress */
        $billingAddress  = $customer->{Customer::FIELD_DEFAULT_BILLING_ADDRESS};
        /** @var Collection $shippingAddress */
        $shippingAddress = $customer->{Customer::FIELD_DEFAULT_SHIPPING_ADDRESS};

        $result[Api::PARAM_DEFAULT_BILLING_ADDRESS] =
            $billingAddress->isEmpty() ? null : $this->addressConverter->convert($billingAddress[0]);

        $result[Api::PARAM_DEFAULT_SHIPPING_ADDRESS] =
            $shippingAddress->isEmpty() ? null : $this->addressConverter->convert($shippingAddress[0]);

        return $result;
    }
}
