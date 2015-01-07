<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\ProductTaxType;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Models\TaxRule as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Api\Taxes\TaxRuleArgs as Args;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxRules implements TaxRulesInterface
{
    use RulesTrait;

    const EVENT_PREFIX = 'Api.TaxRule.';
    const BIND_NAME    = __CLASS__;

    protected static $relations = [
        Model::FIELD_TAX,
        'territories.territory',
        Model::FIELD_POSTCODES,
        'productTypes.type',
        'customerTypes.type',
    ];

    /**
     * @var Model
     */
    private $ruleModel;

    /**
     * @var Country
     */
    private $countryModel;

    /**
     * @var Region
     */
    private $regionModel;

    /**
     * @var CustomerType
     */
    private $customerTypeModel;

    /**
     * @var ProductTaxType
     */
    private $productTypeModel;

    /**
     * @var Tax
     */
    private $taxModel;

    /**
     * @param Model          $ruleModel
     * @param Country        $countryModel
     * @param Region         $regionModel
     * @param CustomerType   $customerTypeModel
     * @param ProductTaxType $productTypeModel
     * @param Tax            $taxModel
     */
    public function __construct(
        Model $ruleModel,
        Country $countryModel,
        Region $regionModel,
        CustomerType $customerTypeModel,
        ProductTaxType $productTypeModel,
        Tax $taxModel
    ) {
        $this->ruleModel         = $ruleModel;
        $this->countryModel      = $countryModel;
        $this->regionModel       = $regionModel;
        $this->customerTypeModel = $customerTypeModel;
        $this->productTypeModel  = $productTypeModel;
        $this->taxModel          = $taxModel;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($taxId, $ruleData, $territories, $postcodes, $customerTypes, $productTypes) = $this->parseTaxRule($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $rule */
            /** @noinspection PhpUndefinedMethodInspection */
            $rule = App::make(Model::BIND_NAME);
            $rule->fill($ruleData);
            $rule->{Tax::FIELD_ID} = $taxId;
            $rule->saveOrFail();
            Permissions::check($rule, Permission::create());

            $this->addRuleFilters($rule->{Model::FIELD_ID}, $territories, $postcodes, $customerTypes, $productTypes);

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new Args(self::EVENT_PREFIX . 'created', $rule));

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function read($ruleId)
    {

        /** @var Model $resource */
        $resource = $this->ruleModel->with(static::$relations)->findOrFail($ruleId);
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($ruleId, array $input)
    {
        list($taxId, $ruleData, $territories, $postcodes, $customerTypes, $productTypes) = $this->parseTaxRule($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $rule */
            $rule = $this->ruleModel->with(static::$relations)->findOrFail($ruleId);
            Permissions::check($rule, Permission::edit());
            $rule->fill($ruleData);
            $rule->{Tax::FIELD_ID} = $taxId;
            $rule->saveOrFail();

            // remove all rule filters for territories, postcodes, customer types and product tax types and ...
            /** @noinspection PhpUndefinedMethodInspection */
            $rule->territories()->delete();
            /** @noinspection PhpUndefinedMethodInspection */
            $rule->postcodes()->delete();
            /** @noinspection PhpUndefinedMethodInspection */
            $rule->customerTypes()->delete();
            /** @noinspection PhpUndefinedMethodInspection */
            $rule->productTypes()->delete();

            // ... add new ones
            $this->addRuleFilters($rule->{Model::FIELD_ID}, $territories, $postcodes, $customerTypes, $productTypes);

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new Args(self::EVENT_PREFIX . 'updated', $rule));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($ruleId)
    {
        /** @var Model $resource */
        $resource = $this->ruleModel->findOrFail($ruleId);
        Permissions::check($resource, Permission::delete());
        $resource->deleteOrFail();

        Event::fire(new Args(self::EVENT_PREFIX . 'deleted', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $resources = $this->ruleModel->with(static::$relations)->get();

        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function parseTaxRule(
        array $input
    ) {
        $taxCode = $this->readAndCheckNotEmpty($input, self::PARAM_TAX_CODE);

        $priority = S\array_get_value($input, self::PARAM_PRIORITY);
        (!empty($priority) and is_numeric($priority)) ?: S\throwEx(new InvalidArgumentException(self::PARAM_PRIORITY));

        // every rule must have at least 1 record (e.g. '*' meaning no restrictions)
        $territories   = $this->readAndCheckNotEmpty($input, self::$rulesTerritories);
        $postcodes     = $this->readAndCheckNotEmpty($input, self::$rulesPostcodes);
        $customerTypes = $this->readAndCheckNotEmpty($input, self::$rulesCustomerTypes);
        $productTypes  = $this->readAndCheckNotEmpty($input, self::$rulesProductTypes);
        unset($input[self::PARAM_TAX_CODE]);
        unset($input[self::$rulesTerritories]);
        unset($input[self::$rulesPostcodes]);
        unset($input[self::$rulesCustomerTypes]);
        unset($input[self::$rulesProductTypes]);

        return [
            $this->taxModel->selectByCode($taxCode)->firstOrFail([Tax::FIELD_ID])->{Tax::FIELD_ID},
            $input,
            $this->parseTerritories($this->countryModel, $this->regionModel, $territories),
            $this->parsePostcodes($postcodes),
            $this->parseCustomerTypes($this->customerTypeModel, $customerTypes),
            $this->parseProductTypes($this->productTypeModel, $productTypes),
        ];
    }

    /**
     * @param array  $input
     * @param string $key
     *
     * @return mixed
     */
    private function readAndCheckNotEmpty(array $input, $key)
    {
        $value = S\array_get_value($input, $key);
        !empty($value) ?: S\throwEx(new InvalidArgumentException($key));
        return $value;
    }

    /**
     * @param int   $ruleId
     * @param array $territories
     * @param array $postcodes
     * @param array $customerTypes
     * @param array $productTypes
     */
    private function addRuleFilters(
        $ruleId,
        array $territories,
        array $postcodes,
        array $customerTypes,
        array $productTypes
    ) {
        /** @var TaxRuleTerritory $territory */
        foreach ($territories as $territory) {
            $territory->{Model::FIELD_ID} = $ruleId;
            $territory->saveOrFail();
        }

        /** @var TaxRulePostcode $postcode */
        foreach ($postcodes as $postcode) {
            $postcode->{Model::FIELD_ID} = $ruleId;
            $postcode->saveOrFail();
        }

        /** @var TaxRuleCustomerType $type */
        foreach ($customerTypes as $type) {
            $type->{Model::FIELD_ID} = $ruleId;
            $type->saveOrFail();
        }

        /** @var TaxRuleProductType $type */
        foreach ($productTypes as $type) {
            $type->{Model::FIELD_ID} = $ruleId;
            $type->saveOrFail();
        }
    }
}
