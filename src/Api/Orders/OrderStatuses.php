<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\OrderStatusRule;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\OrderStatus as Model;
use \Neomerx\Core\Models\OrderStatusRule as RuleModel;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class OrderStatuses implements OrderStatusesInterface
{
    const EVENT_PREFIX = 'Api.OrderStatus.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var RuleModel
     */
    private $ruleModel;

    /**
     * Constructor.
     *
     * @param Model     $model
     * @param RuleModel $ruleModel
     */
    public function __construct(Model $model, RuleModel $ruleModel)
    {
        $this->model = $model;
        $this->ruleModel = $ruleModel;
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

            /** @var Model $status */
            $status = $this->model->createOrFailResource($input);

            Permissions::check($status, Permission::create());

            $canChangeFromId = $status->{Model::FIELD_ID};
            foreach ($ruleCodes as $code) {
                $canChangeToId = $this->model->selectByCode($code)->firstOrFail([Model::FIELD_ID])->{Model::FIELD_ID};
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
        /** @var Model $status */
        /** @noinspection PhpParamsInspection */
        $status = $this->model->selectByCode($code)->withAvailableStatuses()->firstOrFail();
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

        Event::fire(new OrderStatusArgs(self::EVENT_PREFIX . 'updated', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailable(Model $statusFrom, Model $statusTo)
    {
        Permissions::check($statusFrom, Permission::edit());
        Permissions::check($statusTo, Permission::view());

        $rule = $this->addStatusRule($statusFrom->{Model::FIELD_ID}, $statusTo->{Model::FIELD_ID});

        Event::fire(new OrderStatusRuleArgs(self::EVENT_PREFIX . 'addedAvailable', $rule));
    }

    /**
     * {@inheritdoc}
     */
    public function removeAvailable(Model $statusFrom, Model $statusTo)
    {
        Permissions::check($statusFrom, Permission::edit());
        Permissions::check($statusTo, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var OrderStatusRule $rule */
        $rule = $this->ruleModel
            ->where(RuleModel::FIELD_ID_ORDER_STATUS_FROM, '=', $statusFrom->{Model::FIELD_ID})
            ->where(RuleModel::FIELD_ID_ORDER_STATUS_TO, '=', $statusTo->{Model::FIELD_ID})
            ->firstOrFail();

        $rule->deleteOrFail();

        Event::fire(new OrderStatusRuleArgs(self::EVENT_PREFIX .'removedAvailable', $rule));
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

        Event::fire(new OrderStatusArgs(self::EVENT_PREFIX . 'deleted', $status));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $statuses = $this->model->withAvailableStatuses()->get();

        foreach ($statuses as $status) {
            /** @var Model $status */
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
            RuleModel::FIELD_ID_ORDER_STATUS_FROM => $canChangeFromId,
            RuleModel::FIELD_ID_ORDER_STATUS_TO   => $canChangeToId,
        ]);

        return $rule;
    }
}
