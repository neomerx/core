<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Role::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Role $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
