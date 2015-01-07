<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface RolesInterface extends CrudInterface
{
    /**
     * Create role.
     *
     * @param array $input
     *
     * @return Role
     */
    public function create(array $input);

    /**
     * Read role by identifier.
     *
     * @param string $code
     *
     * @return Role
     */
    public function read($code);

    /**
     * Get all user roles in the system.
     *
     * @return Collection
     */
    public function all();
}
