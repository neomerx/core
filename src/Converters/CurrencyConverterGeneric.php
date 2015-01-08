<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\CurrencyProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Currencies\CurrenciesInterface as Api;

class CurrencyConverterGeneric implements ConverterInterface
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
     * @param Currency $currency
     *
     * @return array<mixed,mixed>
     */
    public function convert($currency = null)
    {
        if ($currency === null) {
            return null;
        }

        ($currency instanceof Currency) ?: S\throwEx(new InvalidArgumentException('currency'));

        $result = $currency->attributesToArray();

        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $currency->properties,
            CurrencyProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
