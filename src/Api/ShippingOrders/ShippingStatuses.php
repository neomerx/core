<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\ShippingOrderStatus as Model;

class ShippingStatuses implements ShippingStatusesInterface
{
    const EVENT_PREFIX = 'Api.ShippingStatus.';
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

            /** @var Model $status */
            $status = $this->model->createOrFailResource($input);
            Permissions::check($status, Permission::create());
            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new ShippingStatusArgs(self::EVENT_PREFIX . 'created', $status));

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $status */
        $status = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::view());
        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var Model $status */
        $status = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::edit());
        empty($input) ?: $status->updateOrFail($input);

        Event::fire(new ShippingStatusArgs(self::EVENT_PREFIX . 'updated', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Model $status */
        $status = $this->model->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::delete());
        $status->deleteOrFail();

        Event::fire(new ShippingStatusArgs(self::EVENT_PREFIX . 'deleted', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $statuses = $this->model->all();

        foreach ($statuses as $status) {
            Permissions::check($status, Permission::view());
        }

        return $statuses;
    }
}
