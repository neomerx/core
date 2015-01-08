<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class TaxConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Tax $tax
     *
     * @return array
     */
    public function convert($tax = null)
    {
        if ($tax === null) {
            return null;
        }

        ($tax instanceof Tax) ?: S\throwEx(new InvalidArgumentException('tax'));

        $result = $tax->attributesToArray();
        $rules = [];
        foreach ($tax->rules as $rule) {
            /** @var \Neomerx\Core\Models\TaxRule $rule */
            $rules[] = $rule->attributesToArray();
        }
        $result[Tax::FIELD_RULES] = $rules;

        return $result;
    }
}
