<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Api\Customers\CustomerAddressesInterface as Api;

class AddressConverterCustomer extends AddressConverterGeneric
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Address $address
     *
     * @return array
     */
    public function convert($address = null)
    {
        if ($address === null) {
            return null;
        }

        $result = parent::convert($address);

        /** @noinspection PhpUndefinedFieldInspection */
        $result[Api::PARAM_ADDRESS_TYPE]       = $address->pivot->{CustomerAddress::FIELD_TYPE};
        /** @noinspection PhpUndefinedFieldInspection */
        $result[Api::PARAM_ADDRESS_IS_DEFAULT] = isset($address->pivot->{CustomerAddress::FIELD_IS_DEFAULT});

        return $result;
    }
}
