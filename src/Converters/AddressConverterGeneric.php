<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Address as Model;
use \Neomerx\Core\Api\Addresses\AddressesInterface;
use \Neomerx\Core\Api\Territories\RegionsInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class AddressConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @inheritdoc
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $region = $resource->region;
        $result = $resource->attributesToArray();
        $result[AddressesInterface::PARAM_REGION_CODE] = $region->code;
        $result[RegionsInterface::PARAM_COUNTRY_CODE]  = $region->country->code;

        return $result;
    }
}
