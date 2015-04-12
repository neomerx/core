<?php namespace Neomerx\Core\Repositories\Employees;

use \Validator;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Employee;
use \Neomerx\Core\Exceptions\ValidationException;
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

        $this->validate($attributes, $resource->getInputOnCreateRules());

        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Employee $resource, array $attributes)
    {
        $this->validate($attributes, $resource->getInputOnUpdateRules());

        $this->fillModel($resource, [], $attributes);
    }

    /**
     * @param array $attributes
     * @param array $rules
     *
     * @throws ValidationException
     */
    protected function validate(array $attributes, array $rules)
    {
        $validator = Validator::make($attributes, $rules);
        $validator->fails() === false ?: S\throwEx(new ValidationException($validator));
    }
}
