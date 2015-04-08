<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class ObjectTypeRepository extends IndexBasedResourceRepository implements ObjectTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ObjectType::BIND_NAME);
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
