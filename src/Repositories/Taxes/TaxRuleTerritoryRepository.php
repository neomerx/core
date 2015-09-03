<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class TaxRuleTerritoryRepository extends BaseRepository implements TaxRuleTerritoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRuleTerritory::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(TaxRule $rule, BaseModel $territory)
    {
        return $this->create($this->idOf($rule), get_class($territory), $this->idOf($territory));
    }

    /**
     * @inheritdoc
     */
    public function create($ruleId, $territoryType, $territoryId)
    {
        $territoryType = $this->getTerritoryType($territoryType);

        $resource = $this->createWith([], $this->filterNulls([
            TaxRuleTerritory::FIELD_ID_TAX_RULE    => $ruleId,
            TaxRuleTerritory::FIELD_TERRITORY_TYPE => $territoryType,
            TaxRuleTerritory::FIELD_TERRITORY_ID   => $territoryId,
        ]));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(TaxRuleTerritory $resource, TaxRule $rule = null, BaseModel $territory = null)
    {
        $territoryType = $territory === null ? null : get_class($territory);
        $territoryId   = $this->idOfNullable($territory);

        $this->update($resource, $this->idOf($rule), $territoryType, $territoryId);
    }

    /**
     * @inheritdoc
     */
    public function update(
        TaxRuleTerritory $resource,
        $ruleId = null,
        $territoryType = null,
        Nullable $territoryId = null
    ) {
        $relationships = $this->filterNulls([
            TaxRuleTerritory::FIELD_ID_TAX_RULE    => $ruleId,
            TaxRuleTerritory::FIELD_TERRITORY_TYPE => $territoryType,
        ], [
            TaxRuleTerritory::FIELD_TERRITORY_ID   => $territoryId,
        ]);

        $this->updateWith($resource, [], $relationships);
    }

    /**
     * @param string $territoryType
     *
     * @return string
     */
    private function getTerritoryType($territoryType)
    {
        return S\arrayGetValueEx([
            Country::class => TaxRuleTerritory::TERRITORY_TYPE_COUNTRY,
            Region::class  => TaxRuleTerritory::TERRITORY_TYPE_REGION,
        ], $territoryType);
    }
}
