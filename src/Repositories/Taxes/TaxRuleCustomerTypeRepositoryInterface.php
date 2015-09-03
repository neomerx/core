<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleCustomerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule       $rule
     * @param Nullable|null $type CustomerType
     *
     * @return TaxRuleCustomerType
     */
    public function createWithObjects(TaxRule $rule, Nullable $type = null);

    /**
     * @param int           $ruleId
     * @param Nullable|null $typeId
     *
     * @return TaxRuleCustomerType
     */
    public function create($ruleId, Nullable $typeId = null);

    /**
     * @param TaxRuleCustomerType $resource
     * @param TaxRule|null       $rule
     * @param Nullable|null      $type CustomerType
     *
     * @return void
     */
    public function updateWithObjects(TaxRuleCustomerType $resource, TaxRule $rule = null, Nullable $type = null);

    /**
     * @param TaxRuleCustomerType $resource
     * @param int|null           $ruleId
     * @param Nullable|null      $typeId CustomerType Id
     *
     * @return void
     */
    public function update(TaxRuleCustomerType $resource, $ruleId = null, Nullable $typeId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleCustomerType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
