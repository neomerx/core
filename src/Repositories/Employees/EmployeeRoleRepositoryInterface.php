<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Models\EmployeeRole;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface EmployeeRoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Employee $employee
     * @param Role     $role
     *
     * @return EmployeeRole
     *
     */
    public function instance(Employee $employee, Role $role);

    /**
     * @param EmployeeRole  $resource
     * @param Employee|null $employee
     * @param Role|null     $role
     *
     * @return void
     */
    public function fill(EmployeeRole $resource, Employee $employee = null, Role $role = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return EmployeeRole
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
