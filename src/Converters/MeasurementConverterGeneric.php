<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\MeasurementProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Features\MeasurementsInterface as Api;

class MeasurementConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Measurement $measurement
     *
     * @return array
     */
    public function convert($measurement = null)
    {
        if ($measurement === null) {
            return null;
        }

        ($measurement instanceof Measurement) ?: S\throwEx(new InvalidArgumentException('measurement'));

        $result = $measurement->attributesToArray();

        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $measurement->properties,
            MeasurementProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
