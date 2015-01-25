<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface TaxRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Tax   $tax
     * @param array $attributes
     *
     * @return TaxRule
     */
    public function instance(Tax $tax, array $attributes);

    /**
     * @param TaxRule    $resource
     * @param Tax|null   $tax
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(TaxRule $resource, Tax $tax = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRule
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
