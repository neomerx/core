<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class EmployeeRepository extends IndexBasedResourceRepository implements EmployeeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Employee::class);
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
