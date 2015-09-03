<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Role
     */
    public function create(array $attributes);

    /**
     * @param Role  $resource
     * @param array $attributes
     *
     * @return void
     */
    public function update(Role $resource, array $attributes);

    /**
     * @param int   $index
     * @param array $scopes
     * @param array $columns
     *
     * @return Role
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
