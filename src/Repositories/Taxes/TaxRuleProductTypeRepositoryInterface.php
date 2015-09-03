<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleProductTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxRule       $rule
     * @param Nullable|null $type ProductTaxType
     *
     * @return TaxRuleProductType
     */
    public function createWithObjects(TaxRule $rule, Nullable $type = null);

    /**
     * @param int           $ruleId
     * @param Nullable|null $typeId
     *
     * @return TaxRuleProductType
     */
    public function create($ruleId, Nullable $typeId = null);

    /**
     * @param TaxRuleProductType $resource
     * @param TaxRule|null       $rule
     * @param Nullable|null      $type ProductTaxType
     *
     * @return void
     */
    public function updateWithObjects(TaxRuleProductType $resource, TaxRule $rule = null, Nullable $type = null);

    /**
     * @param TaxRuleProductType $resource
     * @param int|null           $ruleId
     * @param Nullable|null      $typeId ProductTaxType Id
     *
     * @return void
     */
    public function update(TaxRuleProductType $resource, $ruleId = null, Nullable $typeId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRuleProductType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
