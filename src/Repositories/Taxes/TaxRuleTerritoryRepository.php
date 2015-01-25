<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class TaxRuleTerritoryRepository extends IndexBasedResourceRepository implements TaxRuleTerritoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRuleTerritory::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(TaxRule $rule, BaseModel $territory)
    {
        /** @var TaxRuleTerritory $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $rule, $territory);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(TaxRuleTerritory $resource, TaxRule $rule = null, BaseModel $territory = null)
    {
        if ($territory !== null) {
            $resource->{TaxRuleTerritory::FIELD_TERRITORY_ID}   = $territory->getKey();
            $resource->{TaxRuleTerritory::FIELD_TERRITORY_TYPE} = S\arrayGetValueEx([
                Country::class => TaxRuleTerritory::TERRITORY_TYPE_COUNTRY,
                Region::class  => TaxRuleTerritory::TERRITORY_TYPE_REGION,
            ], get_class($territory));
        }

        $this->fillModel($resource, [
            TaxRuleTerritory::FIELD_ID_TAX_RULE => $rule,
        ]);
    }

    /**
     * @param TaxRule $rule
     *
     * @return TaxRuleTerritory
     *
     */
    public function instanceAllCountries(TaxRule $rule)
    {
        /** @var TaxRuleTerritory $resource */
        $resource = $this->makeModel();
        $this->fillAllCountries($resource, $rule);
        return $resource;
    }

    /**
     * @param TaxRule $rule
     *
     * @return TaxRuleTerritory
     *
     */
    public function instanceAllRegions(TaxRule $rule)
    {
        /** @var TaxRuleTerritory $resource */
        $resource = $this->makeModel();
        $this->fillAllRegions($resource, $rule);
        return $resource;
    }

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     *
     * @return void
     */
    public function fillAllCountries(TaxRuleTerritory $resource, TaxRule $rule = null)
    {
        $this->fillTerritory($resource, TaxRuleTerritory::TERRITORY_TYPE_COUNTRY);
        $this->fillModel($resource, [TaxRuleTerritory::FIELD_ID_TAX_RULE => $rule]);
    }

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     *
     * @return void
     */
    public function fillAllRegions(TaxRuleTerritory $resource, TaxRule $rule = null)
    {
        $this->fillTerritory($resource, TaxRuleTerritory::TERRITORY_TYPE_REGION);
        $this->fillModel($resource, [TaxRuleTerritory::FIELD_ID_TAX_RULE => $rule]);
    }

    private function fillTerritory(TaxRuleTerritory $resource, $type)
    {
        if ($resource->exists === true) {
            $resource->{TaxRuleTerritory::FIELD_TERRITORY_ID} = null;
        } else {
            unset($resource[TaxRuleTerritory::FIELD_TERRITORY_ID]);
        }
        $resource->{TaxRuleTerritory::FIELD_TERRITORY_TYPE} = $type;
    }
}
