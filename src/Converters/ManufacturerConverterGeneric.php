<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Manufacturer as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Manufacturers\ManufacturersInterface as Api;
use \Neomerx\Core\Models\ManufacturerProperties as PropertiesModel;

class ManufacturerConverterGeneric implements ConverterInterface
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
        $result[Api::PARAM_ADDRESS]    = $this->addressConverter->convert($resource->address);
        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $resource->properties,
            PropertiesModel::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
