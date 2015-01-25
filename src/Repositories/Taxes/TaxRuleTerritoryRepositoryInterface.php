<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\BaseModel;
use \Neomerx\Core\Models\TaxRuleTerritory;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface TaxRuleTerritoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule   $rule
     * @param BaseModel $territory
     *
     * @return TaxRuleTerritory
     *
     */
    public function instance(TaxRule $rule, BaseModel $territory);

    /**
     * @param TaxRule $rule
     *
     * @return TaxRuleTerritory
     *
     */
    public function instanceAllCountries(TaxRule $rule);

    /**
     * @param TaxRule $rule
     *
     * @return TaxRuleTerritory
     *
     */
    public function instanceAllRegions(TaxRule $rule);

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     * @param BaseModel|null   $territory
     *
     * @return void
     */
    public function fill(TaxRuleTerritory $resource, TaxRule $rule = null, BaseModel $territory = null);

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     *
     * @return void
     */
    public function fillAllCountries(TaxRuleTerritory $resource, TaxRule $rule = null);

    /**
     * @param TaxRuleTerritory $resource
     * @param TaxRule|null     $rule
     *
     * @return void
     */
    public function fillAllRegions(TaxRuleTerritory $resource, TaxRule $rule = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleTerritory
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
