<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\CarrierProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Carriers\CarriersInterface as Api;

class CarrierConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Carrier $carrier
     *
     * @return array
     */
    public function convert($carrier = null)
    {
        if ($carrier === null) {
            return null;
        }

        ($carrier instanceof Carrier) ?: S\throwEx(new InvalidArgumentException('carrier'));

        $result = $carrier->attributesToArray();

        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $carrier->properties,
            CarrierProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
