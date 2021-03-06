<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CustomerRiskRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return CustomerRisk
     */
    public function create(array $attributes);

    /**
     * @param CustomerRisk $resource
     * @param array        $attributes
     *
     * @return void
     */
    public function update(CustomerRisk $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CustomerRisk
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
