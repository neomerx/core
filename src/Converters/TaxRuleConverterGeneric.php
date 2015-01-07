<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Models\TaxRule as Model;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Api\Taxes\TaxRulesInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class TaxRuleConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    const MSG_CODE_RULES_ALL   = 'nm::application.rules_all';

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
        $result[Model::FIELD_TERRITORIES]    = $this->convertTerritories($resource);
        $result[Model::FIELD_POSTCODES]      = $this->convertPostcodes($resource);
        $result[Model::FIELD_PRODUCT_TYPES]  = $this->convertProductTypes($resource);
        $result[Model::FIELD_CUSTOMER_TYPES] = $this->convertCustomerTypes($resource);

        return $result;
    }

    /**
     * @param Model $resource
     *
     * @return array
     */
    private function convertTerritories(Model $resource)
    {
        $tmp = [];
        foreach ($resource->territories as $rule) {
            /** @var TaxRuleTerritory $rule */
            $code = ($rule->territory_id === null ? Api::PARAM_FILTER_ALL : $rule->territory->code);
            $type = substr($rule->territory_type, strrpos($rule->territory_type, '\\') + 1);
            $tmp[] = [
                Api::PARAM_TERRITORY_CODE => $code,
                Api::PARAM_TERRITORY_TYPE => $type,
            ];
        }
        return $tmp;
    }

    /**
     * @param Model $resource
     *
     * @return array
     */
    private function convertPostcodes(Model $resource)
    {
        $tmp = [];
        foreach ($resource->postcodes as $rule) {
            /** @var TaxRulePostcode $rule */
            $tmp[] = [
                TaxRulePostcode::FIELD_POSTCODE_FROM => $rule->postcode_from,
                TaxRulePostcode::FIELD_POSTCODE_TO   => $rule->postcode_to,
                TaxRulePostcode::FIELD_POSTCODE_MASK => $rule->postcode_mask,
            ];
        }
        return $tmp;
    }

    /**
     * @param Model $resource
     *
     * @return array
     */
    private function convertProductTypes(Model $resource)
    {
        $tmp = [];
        foreach ($resource->product_types as $rule) {
            /** @var TaxRuleProductType $rule */
            $type = $rule->type;
            $tmp[] = [
                Api::PARAM_TYPE_CODE => ($type === null ? Api::PARAM_FILTER_ALL : $type->code),
                Api::PARAM_TYPE_NAME => ($type === null ? trans(self::MSG_CODE_RULES_ALL) : $type->name),
            ];
        }
        return $tmp;
    }

    /**
     * @param Model $resource
     *
     * @return array
     */
    private function convertCustomerTypes(Model $resource)
    {
        $tmp = [];
        foreach ($resource->customer_types as $rule) {
            /** @var TaxRuleCustomerType $rule */
            $type = $rule->type;
            $tmp[] = [
                Api::PARAM_TYPE_CODE => ($type === null ? Api::PARAM_FILTER_ALL : $type->code),
                Api::PARAM_TYPE_NAME => ($type === null ? trans(self::MSG_CODE_RULES_ALL) : $type->name),
            ];
        }
        return $tmp;
    }
}
