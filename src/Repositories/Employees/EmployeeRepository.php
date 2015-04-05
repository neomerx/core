<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class EmployeeRepository extends IndexBasedResourceRepository implements EmployeeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Employee::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Employee $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Employee $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
