<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ObjectTypeRepository extends IndexBasedResourceRepository implements ObjectTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ObjectType::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var ObjectType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(ObjectType $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
