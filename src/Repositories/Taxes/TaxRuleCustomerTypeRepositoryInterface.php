<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleCustomerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule           $rule
     * @param CustomerType|null $type
     *
     * @return TaxRuleCustomerType
     */
    public function instance(TaxRule $rule, CustomerType $type = null);

    /**
     * @param TaxRuleCustomerType $resource
     * @param TaxRule|null        $rule
     * @param CustomerType|null   $type
     *
     * @return void
     */
    public function fill(TaxRuleCustomerType $resource, TaxRule $rule = null, CustomerType $type = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleCustomerType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
