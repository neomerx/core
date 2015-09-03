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
    public function createWithObjects(Employee $employee, Role $role);

    /**
     * @param int $employeeId
     * @param int $roleId
     *
     * @return EmployeeRole
     *
     */
    public function create($employeeId, $roleId);

    /**
     * @param EmployeeRole  $resource
     * @param Employee|null $employee
     * @param Role|null     $role
     *
     * @return void
     */
    public function updateWithObjects(EmployeeRole $resource, Employee $employee = null, Role $role = null);

    /**
     * @param EmployeeRole $resource
     * @param int|null     $employeeId
     * @param int|null     $roleId
     *
     * @return void
     */
    public function update(EmployeeRole $resource, $employeeId = null, $roleId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return EmployeeRole
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
