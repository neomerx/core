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
     * @param TaxRule    $rule
     * @param array|null $attributes
     *
     * @return TaxRulePostcode
     */
    public function instance(TaxRule $rule, array $attributes = null);

    /**
     * @param TaxRulePostcode $resource
     * @param TaxRule|null    $rule
     * @param array|null      $attributes
     *
     * @return void
     */
    public function fill(TaxRulePostcode $resource, TaxRule $rule = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRulePostcode
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
