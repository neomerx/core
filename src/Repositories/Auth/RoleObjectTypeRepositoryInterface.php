<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Models\RoleObjectType;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface RoleObjectTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Role       $role
     * @param ObjectType $objectType
     * @param array      $attributes
     *
     * @return RoleObjectType
     */
    public function createWithObjects(Role $role, ObjectType $objectType, array $attributes);

    /**
     * @param int   $roleId
     * @param int   $objectTypeId
     * @param array $attributes
     *
     * @return RoleObjectType
     */
    public function create($roleId, $objectTypeId, array $attributes);

    /**
     * @param RoleObjectType|null $resource
     * @param Role|null           $role
     * @param ObjectType|null     $objectType
     * @param array|null          $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        RoleObjectType $resource,
        Role $role = null,
        ObjectType $objectType = null,
        $attributes = null
    );

    /**
     * @param RoleObjectType|null $resource
     * @param int|null            $roleId
     * @param int|null            $objectTypeId
     * @param array|null          $attributes
     *
     * @return void
     */
    public function update(
        RoleObjectType $resource,
        $roleId = null,
        $objectTypeId = null,
        $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return RoleObjectType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
