<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Models\EmployeeRole;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class EmployeeRoleRepository extends BaseRepository implements EmployeeRoleRepositoryInterface
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
    public function createWithObjects(Employee $employee, Role $role)
    {
        return $this->create($this->idOf($employee), $this->idOf($role));
    }

    /**
     * @inheritdoc
     */
    public function create($employeeId, $roleId)
    {
        $resource = $this->createWith([], $this->getRelationships($employeeId, $roleId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(EmployeeRole $resource, Employee $employee = null, Role $role = null)
    {
        $this->update($resource, $this->idOf($employee), $this->idOf($role));
    }

    /**
     * @inheritdoc
     */
    public function update(EmployeeRole $resource, $employeeId = null, $roleId = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($employeeId, $roleId));
    }

    /**
     * @param int $employeeId
     * @param int $roleId
     *
     * @return array
     */
    protected function getRelationships($employeeId, $roleId)
    {
        return $this->filterNulls([
            EmployeeRole::FIELD_ID_EMPLOYEE => $employeeId,
            EmployeeRole::FIELD_ID_ROLE     => $roleId,
        ]);
    }
}
