<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleTerritoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule   $rule
     * @param BaseModel $territory
     *
     * @return TaxRuleTerritory
     *
     */
    public function createWithObjects(TaxRule $rule, BaseModel $territory);

    /**
     * @param int      $ruleId
     * @param string   $territoryType Class of the territory (country or region)
     * @param int|null $territoryId
     *
     * @return TaxRuleTerritory
     */
    public function create($ruleId, $territoryType, $territoryId);

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     * @param BaseModel|null   $territory
     *
     * @return void
     */
    public function updateWithObjects(TaxRuleTerritory $resource, TaxRule $rule = null, BaseModel $territory = null);

    /**
     * @param TaxRuleTerritory $resource
     * @param int|null         $ruleId
     * @param string|null      $territoryType
     * @param Nullable|null    $territoryId
     */
    public function update(
        TaxRuleTerritory $resource,
        $ruleId = null,
        $territoryType = null,
        Nullable $territoryId = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleTerritory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
