<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleProductTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule             $rule
     * @param ProductTaxType|null $type
     *
     * @return TaxRuleProductType
     */
    public function instance(TaxRule $rule, ProductTaxType $type = null);

    /**
     * @param TaxRuleProductType  $resource
     * @param TaxRule|null        $rule
     * @param ProductTaxType|null $type
     *
     * @return void
     */
    public function fill(TaxRuleProductType $resource, TaxRule $rule = null, ProductTaxType $type = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleProductType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
