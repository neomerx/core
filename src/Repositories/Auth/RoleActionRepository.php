<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Models\Action;
use \Neomerx\Core\Models\RoleAction;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class RoleActionRepository extends IndexBasedResourceRepository implements RoleActionRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(RoleAction::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Role $role, Action $action)
    {
        /** @var RoleAction $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $role, $action);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(RoleAction $resource, Role $role = null, Action $action = null)
    {
        $this->fillModel($resource, [
            RoleAction::FIELD_ID_ACTION => $action,
            RoleAction::FIELD_ID_ROLE   => $role,
        ]);
    }
}
