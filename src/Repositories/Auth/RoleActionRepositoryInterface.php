<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\Action;
use \Neomerx\Core\Models\RoleAction;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface RoleActionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Role   $role
     * @param Action $action
     *
     * @return RoleAction
     *
     */
    public function instance(Role $role, Action $action);

    /**
     * @param Action|null $action
     * @param RoleAction  $resource
     * @param Role|null   $role
     *
     * @return void
     */
    public function fill(RoleAction $resource, Role $role = null, Action $action = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return RoleAction
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
