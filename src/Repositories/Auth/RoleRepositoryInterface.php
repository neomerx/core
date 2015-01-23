<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Role
     */
    public function instance(array $attributes);

    /**
     * @param Role  $resource
     * @param array $attributes
     *
     * @return void
     */
    public function fill(Role $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Role
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
