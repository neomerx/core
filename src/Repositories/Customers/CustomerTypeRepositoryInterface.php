<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CustomerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return CustomerType
     */
    public function create(array $attributes);

    /**
     * @param CustomerType $resource
     * @param array        $attributes
     *
     * @return void
     */
    public function update(CustomerType $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CustomerType
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
