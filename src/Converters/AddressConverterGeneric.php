<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Api\Addresses\AddressesInterface;
use \Neomerx\Core\Api\Territories\RegionsInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class AddressConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Address $address
     *
     * @return null|array<mixed,mixed>
     */
    public function convert($address = null)
    {
        if ($address === null) {
            return null;
        }

        ($address instanceof Address) ?: S\throwEx(new InvalidArgumentException('address'));

        $region = $address->region;
        $result = $address->attributesToArray();
        $result[AddressesInterface::PARAM_REGION_CODE] = $region->code;
        $result[RegionsInterface::PARAM_COUNTRY_CODE]  = $region->country->code;

        return $result;
    }
}
