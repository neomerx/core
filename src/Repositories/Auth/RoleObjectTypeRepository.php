<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Models\RoleObjectType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class RoleObjectTypeRepository extends BaseRepository implements RoleObjectTypeRepositoryInterface
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
    public function createWithObjects(Role $role, ObjectType $objectType, array $attributes)
    {
        return $this->create($this->idOf($role), $this->idOf($objectType), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($roleId, $objectTypeId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($roleId, $objectTypeId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        RoleObjectType $resource,
        Role $role = null,
        ObjectType $objectType = null,
        $attributes = null
    ) {
        $this->update($resource, $this->idOf($role), $this->idOf($objectType), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        RoleObjectType $resource,
        $roleId = null,
        $objectTypeId = null,
        $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($roleId, $objectTypeId));
    }

    /**
     * @param int $roleId
     * @param int $objectTypeId
     *
     * @return array
     */
    protected function getRelationships($roleId, $objectTypeId)
    {
        return $this->filterNulls([
            RoleObjectType::FIELD_ID_TYPE => $objectTypeId,
            RoleObjectType::FIELD_ID_ROLE => $roleId,
        ]);
    }
}
