<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\ShippingOrderStatus;

class ShippingStatuses implements ShippingStatusesInterface
{
    const EVENT_PREFIX = 'Api.ShippingStatus.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var ShippingOrderStatus
     */
    private $shippingOrderStatus;

    /**
     * Constructor.
     *
     * @param ShippingOrderStatus $shippingOrderStatus
     */
    public function __construct(ShippingOrderStatus $shippingOrderStatus)
    {
        $this->shippingOrderStatus = $shippingOrderStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\ShippingOrderStatus $status */
            $status = $this->shippingOrderStatus->createOrFailResource($input);
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
        /** @var \Neomerx\Core\Models\ShippingOrderStatus $status */
        $status = $this->shippingOrderStatus->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::view());
        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var \Neomerx\Core\Models\ShippingOrderStatus $status */
        $status = $this->shippingOrderStatus->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::edit());
        empty($input) ?: $status->updateOrFail($input);

        Event::fire(new ShippingStatusArgs(self::EVENT_PREFIX . 'updated', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\ShippingOrderStatus $status */
        $status = $this->shippingOrderStatus->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::delete());
        $status->deleteOrFail();

        Event::fire(new ShippingStatusArgs(self::EVENT_PREFIX . 'deleted', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $statuses = $this->shippingOrderStatus->all();

        foreach ($statuses as $status) {
            Permissions::check($status, Permission::view());
        }

        return $statuses;
    }
}
