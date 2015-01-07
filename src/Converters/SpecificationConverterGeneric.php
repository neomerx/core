<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Collection;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Models\MeasurementProperties;
use \Neomerx\Core\Models\Specification as Model;
use \Neomerx\Core\Models\CharacteristicProperties;
use \Neomerx\Core\Models\CharacteristicValueProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Products\SpecificationInterface as Api;

class SpecificationConverterGeneric implements ConverterInterface
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
     * Format specification collection to array representation.
     *
     * @param Collection $collection
     *
     * @return array
     */
    public function convert($collection = null)
    {
        if (empty($collection)) {
            return null;
        }

        ($collection instanceof Collection) ?: S\throwEx(new InvalidArgumentException('resource'));

        // convert 'tree' to (possibly) sparse matrix
        list($numberOfSpecRows, $sparseMatrix) = $this->getSparseLanguageSpecRowMatrix($collection);

        // convert sparse matrix to normal/(full size)/(fixed dimensions) matrix
        // dimensions are 3 columns (characteristic, value, measurement) x N rows (languages)
        // each language row contains specification rows in that language
        $result = $this->convertSparseToFullSize($sparseMatrix, $numberOfSpecRows, $this->languageFilter);

        // if we are asked to filter cut language selector
        return $this->languageFilter === null ? $result :
            (isset($result[$this->languageFilter]) ? $result[$this->languageFilter] : null);
    }

    /**
     * Convert 'tree' representation of specification to sparse table view.
     * Returns sparse table and number of found languages.
     *
     * @param Collection $specs
     *
     * @return array
     */
    private function getSparseLanguageSpecRowMatrix(Collection $specs)
    {
        $result = [];
        $index = 0;
        foreach ($specs as $spec) {

            ($spec instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

            /** @var Model $spec */

            $value          = $spec->value;
            $characteristic = $value->characteristic;
            $measurement    = $characteristic->measurement;

            $characteristicCode = $characteristic->code;
            foreach ($characteristic->properties as $characteristicProp) {
                /** @var CharacteristicProperties $characteristicProp */
                $languageCode = $characteristicProp->language->iso_code;
                $nameInLang   = $characteristicProp->name;
                $result[$languageCode][$index][CharacteristicValue::FIELD_CHARACTERISTIC] = [
                    Api::PARAM_PAIR_CODE  => $characteristicCode,
                    Api::PARAM_PAIR_VALUE => $nameInLang
                ];
            }

            $valueCode = $value->code;
            foreach ($value->properties as $valueProp) {
                /** @var CharacteristicValueProperties $valueProp */
                $languageCode = $valueProp->language->iso_code;
                $valueInLang  = $valueProp->value;
                $result[$languageCode][$index][Model::FIELD_VALUE] = [
                    Api::PARAM_PAIR_CODE  => $valueCode,
                    Api::PARAM_PAIR_VALUE => $valueInLang
                ];
            }

            if ($measurement !== null) {
                $measurementCode = $measurement->code;
                foreach ($measurement->properties as $measurementProp) {
                    /** @var MeasurementProperties $measurementProp */
                    $languageCode = $measurementProp->language->iso_code;
                    $nameInLang   = $measurementProp->name;
                    $result[$languageCode][$index][Characteristic::FIELD_MEASUREMENT] = [
                        Api::PARAM_PAIR_CODE  => $measurementCode,
                        Api::PARAM_PAIR_VALUE => $nameInLang
                    ];
                }
            }

            ++$index;
        }

        return [$index, $result];
    }

    /**
     * @param array  $sparseMatrix
     * @param int    $numberOfSpecRows
     * @param string $languageFilter
     *
     * @return array
     */
    private function convertSparseToFullSize(array $sparseMatrix, $numberOfSpecRows, $languageFilter)
    {
        $result = [];
        foreach ($sparseMatrix as $languageCode => $specRows) {
            if ($languageFilter === null or strcasecmp($languageFilter, $languageCode) === 0) {
                for ($i = 0; $i < $numberOfSpecRows; ++$i) {

                    $result[$languageCode][$i] = [

                        Api::PARAM_CHARACTERISTIC =>
                            isset($specRows[$i][CharacteristicValue::FIELD_CHARACTERISTIC]) ?
                                $specRows[$i][CharacteristicValue::FIELD_CHARACTERISTIC] : null,

                        Api::PARAM_VALUE =>
                            isset($specRows[$i][Model::FIELD_VALUE]) ? $specRows[$i][Model::FIELD_VALUE] : null,

                        Api::PARAM_MEASUREMENT =>
                            isset($specRows[$i][Characteristic::FIELD_MEASUREMENT]) ?
                                $specRows[$i][Characteristic::FIELD_MEASUREMENT] : null,

                    ];
                }
            }
        }
        return $result;
    }
}
