<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Features\CharacteristicsInterface as Api;

class CharacteristicConverterGeneric implements ConverterInterface
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var string
     */
    private $languageFilter;

    /**
     * @param string $languageFilter
     */
    public function __construct($languageFilter = null)
    {
        $this->languageFilter = $languageFilter;
    }

    /**
     * @param string $languageFilter
     */
    public function setLanguageFilter($languageFilter)
    {
        $this->languageFilter = $languageFilter;
    }

    /**
     * @return string
     */
    public function getLanguageFilter()
    {
        return $this->languageFilter;
    }

    /**
     * Format model to array representation.
     *
     * @param Characteristic $characteristic
     *
     * @return array
     */
    public function convert($characteristic = null)
    {
        if ($characteristic === null) {
            return null;
        }

        ($characteristic instanceof Characteristic) ?: S\throwEx(new InvalidArgumentException('characteristic'));

        $result = $characteristic->attributesToArray();

        $result[Api::PARAM_MEASUREMENT_CODE] = $characteristic->measurement->code;
        $result[Api::PARAM_PROPERTIES]       = $this->regroupLanguageProperties(
            $characteristic->properties,
            CharacteristicProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
