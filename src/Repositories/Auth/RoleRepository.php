<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class RoleRepository extends CodeBasedResourceRepository implements RoleRepositoryInterface
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
    public function instance(array $attributes)
    {
        /** @var Role $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Role $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
