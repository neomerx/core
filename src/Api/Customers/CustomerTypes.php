<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CustomerType as Model;
use \Neomerx\Core\Api\Customers\CustomerTypeArgs as Args;

class CustomerTypes implements CustomerTypesInterface
{
    const EVENT_PREFIX = 'Api.CustomerType.';
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

            /** @var Model $resource */
            $resource = $this->model->createOrFailResource($input);
            Permissions::check($resource, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new Args(self::EVENT_PREFIX . 'created', $resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $resource */
        $resource = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var Model $resource */
        $resource = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::edit());
        empty($input) ?: $resource->updateOrFail($input);

        Event::fire(new Args(self::EVENT_PREFIX . 'updated', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Model $resource */
        $resource = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::delete());
        $resource->deleteOrFail();

        Event::fire(new Args(self::EVENT_PREFIX . 'deleted', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $resources = $this->model->all();

        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }
}
