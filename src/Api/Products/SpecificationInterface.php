<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;

interface SpecificationInterface
{
    const PARAM_PAIR_CODE      = 'code';
    const PARAM_PAIR_VALUE     = 'value';
    const PARAM_VALUE          = Specification::FIELD_VALUE;
    const PARAM_MEASUREMENT    = Characteristic::FIELD_MEASUREMENT;
    const PARAM_CHARACTERISTIC = CharacteristicValue::FIELD_CHARACTERISTIC;
}
