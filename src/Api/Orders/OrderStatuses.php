<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class OrderStatuses implements OrderStatusesInterface
{
    const EVENT_PREFIX = 'Api.OrderStatus.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var OrderStatus
     */
    private $orderStatusModel;

    /**
     * @var OrderStatusRule
     */
    private $ruleModel;

    /**
     * Constructor.
     *
     * @param OrderStatus     $orderStatus
     * @param OrderStatusRule $ruleModel
     */
    public function __construct(OrderStatus $orderStatus, OrderStatusRule $ruleModel)
    {
        $this->orderStatusModel = $orderStatus;
        $this->ruleModel        = $ruleModel;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        isset($input[self::PARAM_RULE_CODES]) ?: S\throwEx(new InvalidArgumentException(self::PARAM_RULE_CODES));
        $ruleCodes = $input[self::PARAM_RULE_CODES];
        unset($input[self::PARAM_RULE_CODES]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\OrderStatus $status */
            $status = $this->orderStatusModel->createOrFailResource($input);

            Permissions::check($status, Permission::create());

            $canChangeFromId = $status->{OrderStatus::FIELD_ID};
            foreach ($ruleCodes as $code) {
                $canChangeToId = $this->orderStatusModel
                    ->selectByCode($code)
                    ->firstOrFail([OrderStatus::FIELD_ID])
                    ->{OrderStatus::FIELD_ID};
                $this->addStatusRule($canChangeFromId, $canChangeToId);
            }

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new OrderStatusArgs(self::EVENT_PREFIX . 'created', $status));

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\OrderStatus $status */
        /** @noinspection PhpParamsInspection */
        $status = $this->orderStatusModel->selectByCode($code)->withAvailableStatuses()->firstOrFail();
        Permissions::check($status, Permission::view());
        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var \Neomerx\Core\Models\OrderStatus $status */
        $status = $this->orderStatusModel->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::edit());
        empty($input) ?: $status->updateOrFail($input);

        Event::fire(new OrderStatusArgs(self::EVENT_PREFIX . 'updated', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailable(OrderStatus $statusFrom, OrderStatus $statusTo)
    {
        Permissions::check($statusFrom, Permission::edit());
        Permissions::check($statusTo, Permission::view());

        $rule = $this->addStatusRule($statusFrom->{OrderStatus::FIELD_ID}, $statusTo->{OrderStatus::FIELD_ID});

        Event::fire(new OrderStatusRuleArgs(self::EVENT_PREFIX . 'addedAvailable', $rule));
    }

    /**
     * {@inheritdoc}
     */
    public function removeAvailable(OrderStatus $statusFrom, OrderStatus $statusTo)
    {
        Permissions::check($statusFrom, Permission::edit());
        Permissions::check($statusTo, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\OrderStatusRule $rule */
        $rule = $this->ruleModel->where([
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM => $statusFrom->{OrderStatus::FIELD_ID},
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO   => $statusTo->{OrderStatus::FIELD_ID}
        ])->firstOrFail();

        $rule->deleteOrFail();

        Event::fire(new OrderStatusRuleArgs(self::EVENT_PREFIX .'removedAvailable', $rule));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\OrderStatus $status */
        $status = $this->orderStatusModel->selectByCode($code)->firstOrFail();
        Permissions::check($status, Permission::delete());
        $status->deleteOrFail();

        Event::fire(new OrderStatusArgs(self::EVENT_PREFIX . 'deleted', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $statuses = $this->orderStatusModel->withAvailableStatuses()->get();

        foreach ($statuses as $status) {
            /** @var \Neomerx\Core\Models\OrderStatus $status */
            Permissions::check($status, Permission::view());
        }

        return $statuses;
    }

    /**
     * @param int $canChangeFromId
     * @param int $canChangeToId
     *
     * @return OrderStatusRule
     */
    private function addStatusRule($canChangeFromId, $canChangeToId)
    {
        settype($canChangeToId, 'int');
        settype($canChangeFromId, 'int');

        $canChangeFromId !== $canChangeToId ?: S\throwEx(new InvalidArgumentException('canChangeTo'));

        $rule = $this->ruleModel->createOrFailResource([
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM => $canChangeFromId,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO   => $canChangeToId,
        ]);

        return $rule;
    }
}
