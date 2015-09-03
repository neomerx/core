<?php namespace Neomerx\Core\Repositories\Employees;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
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
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Employee $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
