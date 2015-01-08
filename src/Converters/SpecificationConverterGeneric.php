<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\Characteristic;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Products\SpecificationInterface as Api;

class SpecificationConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

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

        ($collection instanceof Collection) ?: S\throwEx(new InvalidArgumentException('collection'));

        // convert 'tree' to (possibly) sparse matrix
        list($numberOfSpecRows, $sparseMatrix) = $this->getSparseLanguageSpecRowMatrix($collection);

        $languageFilter = $this->getLanguageFilter();

        // convert sparse matrix to normal/(full size)/(fixed dimensions) matrix
        // dimensions are 3 columns (characteristic, value, measurement) x N rows (languages)
        // each language row contains specification rows in that language
        $result = $this->convertSparseToFullSize($sparseMatrix, $numberOfSpecRows, $languageFilter);

        // if we are asked to filter cut language selector
        return $languageFilter === null ? $result :
            (isset($result[$languageFilter]) ? $result[$languageFilter] : null);
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

            ($spec instanceof Specification) ?: S\throwEx(new InvalidArgumentException('specs'));

            /** @var \Neomerx\Core\Models\Specification $spec */

            $value          = $spec->value;
            $characteristic = $value->characteristic;
            $measurement    = $characteristic->measurement;

            $characteristicCode = $characteristic->code;
            foreach ($characteristic->properties as $characteristicProp) {
                /** @var \Neomerx\Core\Models\CharacteristicProperties $characteristicProp */
                $languageCode = $characteristicProp->language->iso_code;
                $nameInLang   = $characteristicProp->name;
                $result[$languageCode][$index][CharacteristicValue::FIELD_CHARACTERISTIC] = [
                    Api::PARAM_PAIR_CODE  => $characteristicCode,
                    Api::PARAM_PAIR_VALUE => $nameInLang
                ];
            }

            $valueCode = $value->code;
            foreach ($value->properties as $valueProp) {
                /** @var \Neomerx\Core\Models\CharacteristicValueProperties $valueProp */
                $languageCode = $valueProp->language->iso_code;
                $valueInLang  = $valueProp->value;
                $result[$languageCode][$index][Specification::FIELD_VALUE] = [
                    Api::PARAM_PAIR_CODE  => $valueCode,
                    Api::PARAM_PAIR_VALUE => $valueInLang
                ];
            }

            if ($measurement !== null) {
                $measurementCode = $measurement->code;
                foreach ($measurement->properties as $measurementProp) {
                    /** @var \Neomerx\Core\Models\MeasurementProperties $measurementProp */
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
                            isset($specRows[$i][Specification::FIELD_VALUE]) ?
                                $specRows[$i][Specification::FIELD_VALUE] : null,

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
