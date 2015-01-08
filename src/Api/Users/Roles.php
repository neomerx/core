<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;

class Roles implements RolesInterface
{
    const EVENT_PREFIX = 'Api.Roles.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Role
     */
    private $role;

    /**
     * Constructor.
     *
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Role $role */
            $role = $this->role->createOrFailResource($input);
            Permissions::check($role, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new RoleArgs(self::EVENT_PREFIX . 'created', $role));

        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\Role $role */
        $role = $this->role->selectByCode($code)->firstOrFail();
        Permissions::check($role, Permission::view());
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var \Neomerx\Core\Models\Role $role */
        $role = $this->role->selectByCode($code)->firstOrFail();
        Permissions::check($role, Permission::edit());
        empty($input) ?: $role->updateOrFail($input);

        Event::fire(new RoleArgs(self::EVENT_PREFIX . 'updated', $role));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($isoCode)
    {
        /** @var \Neomerx\Core\Models\Role $role */
        $role = $this->role->selectByCode($isoCode)->firstOrFail();
        Permissions::check($role, Permission::delete());
        $role->deleteOrFail();

        Event::fire(new RoleArgs(self::EVENT_PREFIX . 'deleted', $role));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $roles = $this->role->all();

        foreach ($roles as $role) {
            Permissions::check($role, Permission::view());
        }

        return $roles;
    }
}
