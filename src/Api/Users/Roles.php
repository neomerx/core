<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Role as Model;

class Roles implements RolesInterface
{
    const EVENT_PREFIX = 'Api.Roles.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * Constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $role */
            $role = $this->model->createOrFailResource($input);
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
        /** @var Model $role */
        $role = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($role, Permission::view());
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var Model $role */
        $role = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($role, Permission::edit());
        empty($input) ?: $role->updateOrFail($input);

        Event::fire(new RoleArgs(self::EVENT_PREFIX . 'updated', $role));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($isoCode)
    {
        /** @var Model $role */
        $role = $this->model->selectByCode($isoCode)->firstOrFail();
        Permissions::check($role, Permission::delete());
        $role->deleteOrFail();

        Event::fire(new RoleArgs(self::EVENT_PREFIX . 'deleted', $role));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $roles = $this->model->all();

        foreach ($roles as $role) {
            Permissions::check($role, Permission::view());
        }

        return $roles;
    }
}
