<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Products\ProductsInterface as Api;

class ProductConverterGeneric implements ConverterInterface
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
     * @param Product $product
     *
     * @return array
     */
    public function convert($product = null)
    {
        if ($product === null) {
            return null;
        }

        ($product instanceof Product) ?: S\throwEx(new InvalidArgumentException('product'));

        $result = $product->attributesToArray();

        $result[Api::PARAM_MANUFACTURER_CODE]     = $product->manufacturer->code;
        $result[Api::PARAM_DEFAULT_CATEGORY_CODE] = $product->default_category->code;
        $result[Api::PARAM_TAX_TYPE_CODE]         = $product->tax_type->code;
        $result[Api::PARAM_PROPERTIES]            = $this->regroupLanguageProperties(
            $product->properties,
            ProductProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
