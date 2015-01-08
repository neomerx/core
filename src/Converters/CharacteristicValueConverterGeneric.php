<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Api\Features\ValuesInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CharacteristicValueProperties;

class CharacteristicValueConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param CharacteristicValue $charValue
     *
     * @return array
     */
    public function convert($charValue = null)
    {
        if ($charValue === null) {
            return null;
        }

        ($charValue instanceof CharacteristicValue) ?: S\throwEx(new InvalidArgumentException('charValue'));

        $result = $charValue->attributesToArray();

        $result[Api::PARAM_CHARACTERISTIC_CODE] = $charValue->characteristic->code;
        $result[Api::PARAM_PROPERTIES]          = $this->regroupLanguageProperties(
            $charValue->properties,
            CharacteristicValueProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
