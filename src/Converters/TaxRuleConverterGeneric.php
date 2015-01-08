<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Api\Taxes\TaxRulesInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class TaxRuleConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    const MSG_CODE_RULES_ALL   = 'nm::application.rules_all';

    /**
     * Format model to array representation.
     *
     * @param TaxRule $taxRule
     *
     * @return array
     */
    public function convert($taxRule = null)
    {
        if ($taxRule === null) {
            return null;
        }

        ($taxRule instanceof TaxRule) ?: S\throwEx(new InvalidArgumentException('taxRule'));

        $result = $taxRule->attributesToArray();
        $result[TaxRule::FIELD_TERRITORIES]    = $this->convertTerritories($taxRule);
        $result[TaxRule::FIELD_POSTCODES]      = $this->convertPostcodes($taxRule);
        $result[TaxRule::FIELD_PRODUCT_TYPES]  = $this->convertProductTypes($taxRule);
        $result[TaxRule::FIELD_CUSTOMER_TYPES] = $this->convertCustomerTypes($taxRule);

        return $result;
    }

    /**
     * @param TaxRule $taxRule
     *
     * @return array
     */
    private function convertTerritories(TaxRule $taxRule)
    {
        $tmp = [];
        foreach ($taxRule->territories as $rule) {
            /** @var \Neomerx\Core\Models\TaxRuleTerritory $rule */
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
     * @param TaxRule $taxRule
     *
     * @return array
     */
    private function convertPostcodes(TaxRule $taxRule)
    {
        $tmp = [];
        foreach ($taxRule->postcodes as $rule) {
            /** @var \Neomerx\Core\Models\TaxRulePostcode $rule */
            $tmp[] = [
                TaxRulePostcode::FIELD_POSTCODE_FROM => $rule->postcode_from,
                TaxRulePostcode::FIELD_POSTCODE_TO   => $rule->postcode_to,
                TaxRulePostcode::FIELD_POSTCODE_MASK => $rule->postcode_mask,
            ];
        }
        return $tmp;
    }

    /**
     * @param TaxRule $taxRule
     *
     * @return array
     */
    private function convertProductTypes(TaxRule $taxRule)
    {
        $tmp = [];
        foreach ($taxRule->product_types as $rule) {
            /** @var \Neomerx\Core\Models\TaxRuleProductType $rule */
            $type = $rule->type;
            $tmp[] = [
                Api::PARAM_TYPE_CODE => ($type === null ? Api::PARAM_FILTER_ALL : $type->code),
                Api::PARAM_TYPE_NAME => ($type === null ? trans(self::MSG_CODE_RULES_ALL) : $type->name),
            ];
        }
        return $tmp;
    }

    /**
     * @param TaxRule $taxRule
     *
     * @return array
     */
    private function convertCustomerTypes(TaxRule $taxRule)
    {
        $tmp = [];
        foreach ($taxRule->customer_types as $rule) {
            /** @var \Neomerx\Core\Models\TaxRuleCustomerType $rule */
            $type = $rule->type;
            $tmp[] = [
                Api::PARAM_TYPE_CODE => ($type === null ? Api::PARAM_FILTER_ALL : $type->code),
                Api::PARAM_TYPE_NAME => ($type === null ? trans(self::MSG_CODE_RULES_ALL) : $type->name),
            ];
        }
        return $tmp;
    }
}
