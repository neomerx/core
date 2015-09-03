<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRulePostcodeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule $rule
     * @param array   $attributes
     *
     * @return TaxRulePostcode
     */
    public function createWithObjects(TaxRule $rule, array $attributes = []);

    /**
     * @param int   $ruleId
     * @param array $attributes
     *
     * @return TaxRulePostcode
     */
    public function create($ruleId, array $attributes = []);

    /**
     * @param TaxRulePostcode $resource
     * @param TaxRule|null    $rule
     * @param array|null      $attributes
     *
     * @return void
     */
    public function updateWithObjects(TaxRulePostcode $resource, TaxRule $rule = null, array $attributes = []);

    /**
     * @param TaxRulePostcode $resource
     * @param int|null        $ruleId
     * @param array|null      $attributes
     *
     * @return void
     */
    public function update(TaxRulePostcode $resource, $ruleId = null, array $attributes = []);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRulePostcode
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
