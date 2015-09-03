<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface TaxRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Tax   $tax
     * @param array $attributes
     *
     * @return TaxRule
     */
    public function createWithObjects(Tax $tax, array $attributes);

    /**
     * @param int   $taxId
     * @param array $attributes
     *
     * @return TaxRule
     */
    public function create($taxId, array $attributes);

    /**
     * @param TaxRule    $resource
     * @param Tax|null   $tax
     * @param array|null $attributes
     *
     * @return void
     */
    public function updateWithObjects(TaxRule $resource, Tax $tax = null, array $attributes = null);

    /**
     * @param TaxRule    $resource
     * @param int|null   $taxId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(TaxRule $resource, $taxId = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return TaxRule
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
