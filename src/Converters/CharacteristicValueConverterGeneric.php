<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Api\Features\ValuesInterface as Api;
use \Neomerx\Core\Models\CharacteristicValue as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CharacteristicValueProperties as PropertiesModel;

class CharacteristicValueConverterGeneric implements ConverterInterface
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
     * @param Model $resource
     *
     * @return array
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $result = $resource->attributesToArray();

        $result[Api::PARAM_CHARACTERISTIC_CODE] = $resource->characteristic->code;
        $result[Api::PARAM_PROPERTIES]          = $this->regroupLanguageProperties(
            $resource->properties,
            PropertiesModel::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
