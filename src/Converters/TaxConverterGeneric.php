<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\Tax as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class TaxConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @inheritdoc
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $result = $resource->attributesToArray();
        $rules = [];
        foreach ($resource->rules as $rule) {
            /** @var TaxRule $rule */
            $rules[] = $rule->attributesToArray();
        }
        $result[Model::FIELD_RULES] = $rules;

        return $result;
    }
}
