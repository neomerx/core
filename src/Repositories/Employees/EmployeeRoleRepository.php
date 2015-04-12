<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Models\EmployeeRole;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class EmployeeRoleRepository extends IndexBasedResourceRepository implements EmployeeRoleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(EmployeeRole::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Employee $employee, Role $role)
    {
        /** @var EmployeeRole $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $employee, $role);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(EmployeeRole $resource, Employee $employee = null, Role $role = null)
    {
        $this->fillModel($resource, [
            EmployeeRole::FIELD_ID_EMPLOYEE => $employee,
            EmployeeRole::FIELD_ID_ROLE     => $role,
        ]);
    }
}
