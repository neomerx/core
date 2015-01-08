<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Customers\CustomerAddressesInterface as Api;

class AddressConverterCustomer extends AddressConverterGeneric
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param CustomerAddress $customerAddress
     *
     * @return null|array<mixed,mixed>
     */
    public function convert($customerAddress = null)
    {
        if ($customerAddress === null) {
            return null;
        }

        ($customerAddress instanceof CustomerAddress) ?: S\throwEx(new InvalidArgumentException('customerAddress'));

        $result = array_merge(parent::convert($customerAddress->address), [
            Api::PARAM_ADDRESS_TYPE       => $customerAddress->{CustomerAddress::FIELD_TYPE},
            Api::PARAM_ADDRESS_IS_DEFAULT => isset($customerAddress->{CustomerAddress::FIELD_IS_DEFAULT}),
        ]);

        return $result;
    }
}
