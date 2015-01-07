<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\User;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface UsersInterface extends CrudInterface
{
    /**
     * Parameter key.
     */
    const PARAM_ROLES  = 'roles';

    /**
     * Event prefix.
     */
    const EVENT_PREFIX = 'Api.Users.';

    /**
     * Create user.
     *
     * @param array $input
     *
     * @return User
     */
    public function create(array $input);

    /**
     * Read resource by identifier.
     *
     * @param int $userId
     *
     * @return User
     */
    public function read($userId);

    /**
     * Search users.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
