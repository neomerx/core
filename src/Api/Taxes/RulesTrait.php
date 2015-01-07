<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\ProductTaxType;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

trait RulesTrait
{
    private static $ruleFilterAll = '*';

    private static $rulesTax           = 'tax';
    private static $rulesTerritories   = 'territories';
    private static $rulesPostcodes     = 'postcodes';
    private static $rulesProductTypes  = 'productTypes';
    private static $rulesCustomerTypes = 'customerTypes';

    private static $formatTerritoryCode    = 'code';
    private static $formatTerritoryType    = 'type';
    private static $formatTerritoryCountry = 'Country';
    private static $formatTerritoryRegion  = 'Region';

    private static $formatPostcodeFrom = 'from';
    private static $formatPostcodeTo   = 'to';
    private static $formatPostcodeMask = 'mask';

    private static $formatTypeCode = 'code';
    private static $formatTypeName = 'name';

    /**
     * @param Country $countryModel
     * @param Region  $regionModel
     * @param array   $territories
     *
     * @return array
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     */
    private function parseTerritories(Country $countryModel, Region $regionModel, array $territories)
    {
        $result = [];
        foreach ($territories as $territory) {
            // convert territory code and type from input...
            $inputCode = S\array_get_value($territory, self::$formatTerritoryCode);
            is_string($inputCode) ?: S\throwEx(new InvalidArgumentException(self::$rulesTerritories));

            $inputType = S\array_get_value($territory, self::$formatTerritoryType);
            is_string($inputType) ?: S\throwEx(new InvalidArgumentException(self::$rulesTerritories));

            /** @noinspection PhpUndefinedMethodInspection */
            $territory = App::make(TaxRuleTerritory::BIND_NAME);

            // ... to model key values
            switch($inputType) {
                case self::$formatTerritoryCountry:
                    $territory->territory_type = TaxRuleTerritory::TERRITORY_TYPE_COUNTRY;
                    if ($inputCode !== self::$ruleFilterAll) {
                        $territory->territory_id = $countryModel->selectByCode($inputCode)
                            ->firstOrFail([Country::FIELD_ID])->{Country::FIELD_ID};
                    }
                    break;
                case self::$formatTerritoryRegion:
                    $territory->territory_type = TaxRuleTerritory::TERRITORY_TYPE_REGION;
                    if ($inputCode !== self::$ruleFilterAll) {
                        $territory->territory_id = $regionModel->selectByCode($inputCode)
                            ->firstOrFail([Region::FIELD_ID])->{Region::FIELD_ID};
                    }
                    break;
                default:
                    throw new InvalidArgumentException(self::$rulesTerritories);
            }

            $result[] = $territory;
        }

        return $result;
    }

    /**
     * @param array $postcodes
     *
     * @return array
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function parsePostcodes(array $postcodes)
    {
        $result = [];
        foreach ($postcodes as $postcode) {

            // Possible cases
            // - no postcode restrictions       =>   to = from = mask = null
            // - from|to or mask restrictions   =>   to/from/mask might be set

            $inputFrom = S\array_get_value($postcode, self::$formatPostcodeFrom);
            $inputTo   = S\array_get_value($postcode, self::$formatPostcodeTo);
            $inputMask = S\array_get_value($postcode, self::$formatPostcodeMask);

            // check all inputs are either strings or nulls
            if ((!is_numeric($inputFrom) and $inputFrom !== null) or
                (!is_numeric($inputTo) and $inputTo !== null) or
                (!is_string($inputMask) and $inputMask !== null)
            ) {
                throw new InvalidArgumentException(self::$rulesPostcodes);
            }

            $inputFrom = trim($inputFrom);
            $inputTo   = trim($inputTo);
            $inputMask = trim($inputMask);

            $inputFrom = empty($inputFrom) ? null : $inputFrom;
            $inputTo   = empty($inputTo)   ? null : $inputTo;
            $inputMask = empty($inputMask) ? null : $inputMask;

            /** @var TaxRulePostcode $taxRulePostCode */
            /** @noinspection PhpUndefinedMethodInspection */
            $taxRulePostCode = App::make(TaxRulePostcode::BIND_NAME);
            $taxRulePostCode->fill(S\array_filter_nulls([
                TaxRulePostcode::FIELD_POSTCODE_FROM => $inputFrom,
                TaxRulePostcode::FIELD_POSTCODE_TO   => $inputTo,
                TaxRulePostcode::FIELD_POSTCODE_MASK => $inputMask
            ]));
            $result[] = $taxRulePostCode;
        }

        return $result;
    }

    /**
     * @param CustomerType $typeModel
     * @param array        $customerTypes
     *
     * @return array
     */
    private function parseCustomerTypes(CustomerType $typeModel, array $customerTypes)
    {
        $result = [];
        foreach ($customerTypes as $typeCode) {
            /** @var TaxRuleCustomerType $rule */
            /** @noinspection PhpUndefinedMethodInspection */
            $rule = App::make(TaxRuleCustomerType::BIND_NAME);
            if ($typeCode !== self::$ruleFilterAll) {
                $rule->{CustomerType::FIELD_ID} = $typeModel->selectByCode($typeCode)
                    ->firstOrFail([CustomerType::FIELD_ID])->{CustomerType::FIELD_ID};
            }
            $result[] = $rule;
        }

        return $result;
    }

    /**
     * @param ProductTaxType $typeModel
     * @param array          $productTypes
     *
     * @return array
     */
    private function parseProductTypes(ProductTaxType $typeModel, array $productTypes)
    {
        $result = [];
        foreach ($productTypes as $typeCode) {
            /** @var TaxRuleProductType $rule */
            /** @noinspection PhpUndefinedMethodInspection */
            $rule = App::make(TaxRuleProductType::BIND_NAME);
            if ($typeCode !== self::$ruleFilterAll) {
                $rule->{ProductTaxType::FIELD_ID} = $typeModel->selectByCode($typeCode)
                    ->firstOrFail([ProductTaxType::FIELD_ID])->{ProductTaxType::FIELD_ID};
            }
            $result[] = $rule;
        }

        return $result;
    }
}
