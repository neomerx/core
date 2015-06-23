<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Models\RoleObjectType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class RoleObjectTypeRepository extends IndexBasedResourceRepository implements RoleObjectTypeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(RoleObjectType::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Role $role, ObjectType $objectType, array $attributes)
    {
        /** @var RoleObjectType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $role, $objectType, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        RoleObjectType $resource,
        Role $role = null,
        ObjectType $objectType = null,
        $attributes = null
    ) {
        $this->fillModel($resource, [
            RoleObjectType::FIELD_ID_TYPE => $objectType,
            RoleObjectType::FIELD_ID_ROLE => $role,
        ], $attributes);
    }
}
