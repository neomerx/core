<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ManufacturerProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Manufacturers\ManufacturersInterface as Api;

class ManufacturerConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

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
        parent::__construct($languageFilter);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->addressConverter = $addressConverter ? $addressConverter : App::make(AddressConverterGeneric::BIND_NAME);
    }

    /**
     * Format model to array representation.
     *
     * @param Manufacturer $manufacturer
     *
     * @return array
     */
    public function convert($manufacturer = null)
    {
        if ($manufacturer === null) {
            return null;
        }

        ($manufacturer instanceof Manufacturer) ?: S\throwEx(new InvalidArgumentException('manufacturer'));

        $result = $manufacturer->attributesToArray();
        $result[Api::PARAM_ADDRESS]    = $this->addressConverter->convert($manufacturer->address);
        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $manufacturer->properties,
            ManufacturerProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
