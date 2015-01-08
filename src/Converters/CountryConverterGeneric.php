<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\CountryProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Territories\CountriesInterface as Api;

class CountryConverterGeneric implements ConverterInterface
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
     * @param Country $country
     *
     * @return array
     */
    public function convert($country = null)
    {
        if ($country === null) {
            return null;
        }

        ($country instanceof Country) ?: S\throwEx(new InvalidArgumentException('country'));

        $result = $country->attributesToArray();

        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $country->properties,
            CountryProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
