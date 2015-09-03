<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Employee
     */
    public function create(array $attributes);

    /**
     * @param Employee $resource
     * @param array    $attributes
     *
     * @return void
     */
    public function update(Employee $resource, array $attributes);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Employee
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
