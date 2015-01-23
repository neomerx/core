<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Employee
     */
    public function instance(array $attributes);

    /**
     * @param Employee $resource
     * @param array    $attributes
     *
     * @return void
     */
    public function fill(Employee $resource, array $attributes);

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Employee
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
