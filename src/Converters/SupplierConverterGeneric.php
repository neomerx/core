<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Supplier;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\SupplierProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Suppliers\SuppliersInterface as Api;

class SupplierConverterGeneric implements ConverterInterface
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var string
     */
    private $languageFilter;

    /**
     * @var ConverterInterface
     */
    private $addressConverter;

    /**
     * @param string             $languageFilter
     * @param ConverterInterface $addressConverter
     */
    public function __construct($languageFilter = null, ConverterInterface $addressConverter = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->addressConverter = $addressConverter ? $addressConverter : App::make(AddressConverterGeneric::BIND_NAME);
        $this->languageFilter   = $languageFilter;
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
     * @param Supplier $supplier
     *
     * @return array
     */
    public function convert($supplier = null)
    {
        if ($supplier === null) {
            return null;
        }

        ($supplier instanceof Supplier) ?: S\throwEx(new InvalidArgumentException('supplier'));

        $result = $supplier->attributesToArray();
        $result[Api::PARAM_ADDRESS]    = $this->addressConverter->convert($supplier->address);
        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $supplier->properties,
            SupplierProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
