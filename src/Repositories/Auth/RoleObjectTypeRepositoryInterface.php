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
    public function instance(Role $role, ObjectType $objectType, array $attributes);

    /**
     * @param ObjectType|null $objectType
     * @param RoleObjectType  $resource
     * @param Role|null       $role
     *
     * @return void
     */
    public function fill(RoleObjectType $resource, Role $role = null, ObjectType $objectType = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return RoleObjectType
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
