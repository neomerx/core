<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Territories\RegionsInterface as Api;

class RegionConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Region $region
     *
     * @return array
     */
    public function convert($region = null)
    {
        if ($region === null) {
            return null;
        }

        ($region instanceof Region) ?: S\throwEx(new InvalidArgumentException('region'));

        $result = $region->attributesToArray();
        $result[Api::PARAM_COUNTRY_CODE] = $region->country->code;

        return $result;
    }
}
