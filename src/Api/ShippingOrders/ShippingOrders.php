<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Api\Facades\Carriers;
use \Neomerx\Core\Support\SearchParser;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Neomerx\Core\Models\Order as OrderModel;
use \Neomerx\Core\Api\Carriers\CarriersInterface;
use \Neomerx\Core\Models\Carrier as CarrierModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\OrderDetails as OrderDetailsModel;
use \Neomerx\Core\Models\ShippingOrder as ShippingOrderModel;
use \Neomerx\Core\Models\ShippingOrderStatus as ShippingOrderStatusModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingOrders implements ShippingOrdersInterface
{
    const EVENT_PREFIX = 'Api.ShippingOrder.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var OrderModel
     */
    private $orderModel;

    /**
     * @var ShippingOrderModel
     */
    private $shippingOrderModel;

    /**
     * @var ShippingOrderStatusModel
     */
    private $shippingOrderStatusModel;

    /**
     * @var CarrierModel
     */
    private $carrierModel;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        ShippingOrderModel::FIELD_ID_ORDER        => SearchGrammar::TYPE_INT,
        ShippingOrderModel::FIELD_TRACKING_NUMBER => SearchGrammar::TYPE_STRING,
        'created'                                 => [SearchGrammar::TYPE_DATE, ShippingOrderModel::FIELD_CREATED_AT],
        'updated'                                 => [SearchGrammar::TYPE_DATE, ShippingOrderModel::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP                 => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE                 => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param OrderModel               $order
     * @param CarrierModel             $carrier
     * @param ShippingOrderModel       $shippingOrder
     * @param ShippingOrderStatusModel $shippingOrderStatus
     */
    public function __construct(
        OrderModel $order,
        CarrierModel $carrier,
        ShippingOrderModel $shippingOrder,
        ShippingOrderStatusModel $shippingOrderStatus
    ) {
        $this->orderModel               = $order;
        $this->carrierModel             = $carrier;
        $this->shippingOrderModel       = $shippingOrder;
        $this->shippingOrderStatusModel = $shippingOrderStatus;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function create(array $input)
    {
        $orderId             =  S\array_get_value($input, OrderModel::FIELD_ID);
        $orderId            !== null ?: S\throwEx(new InvalidArgumentException(OrderModel::FIELD_ID));

        $carrierCode         =  S\array_get_value($input, self::PARAM_CARRIER_CODE);
        $carrierCode        !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_CARRIER_CODE));

        $trackingNumber      =  S\array_get_value($input, self::PARAM_TRACKING_NUMBER);
        $trackingNumber     !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_TRACKING_NUMBER));

        $shippingStatusCode  = S\array_get_value($input, self::PARAM_STATUS_CODE);
        $shippingStatusCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_STATUS_CODE));

        // if null then all inventory for the order would be added to shipping order
        $detailsIds =  S\array_get_value($input, self::PARAM_DETAIL_IDS);

        $statusId = $this->shippingOrderStatusModel->selectByCode($shippingStatusCode)
            ->firstOrFail([shippingOrderStatusModel::FIELD_ID])->{shippingOrderStatusModel::FIELD_ID};

        /** @var OrderModel $order */
        $order = $this->orderModel->findOrFail($orderId);
        Permissions::check($order, Permission::edit());

        /** @var CarrierModel $carrier */
        $carrier = $this->carrierModel->selectByCode($carrierCode)->firstOrFail([CarrierModel::FIELD_ID]);
        Permissions::check($carrier, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var ShippingOrderModel $shippingOrder */
            $shippingOrder = $this->shippingOrderModel->createOrFailResource([
                ShippingOrderModel::FIELD_ID_ORDER                 => $orderId,
                ShippingOrderModel::FIELD_ID_CARRIER               => $carrier->{CarrierModel::FIELD_ID},
                ShippingOrderModel::FIELD_ID_SHIPPING_ORDER_STATUS => $statusId,
                ShippingOrderModel::FIELD_TRACKING_NUMBER          => $trackingNumber,
            ]);
            Permissions::check($shippingOrder, Permission::create());
            $shippingOrderId = $shippingOrder->{ShippingOrderModel::FIELD_ID};

            // update order details with shipping order info
            /** @noinspection PhpUndefinedMethodInspection */
            $detailsRelation = $order->details()->whereNull(OrderDetailsModel::FIELD_ID_SHIPPING_ORDER);

            // if specific order details IDs were specified will add it to the filter
            if (!empty($detailsIds)) {
                /** @noinspection PhpUndefinedMethodInspection */
                $detailsRelation = $detailsRelation->whereIn(OrderDetailsModel::FIELD_ID, $detailsIds);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $detailsRelation->update([ShippingOrderModel::FIELD_ID => $shippingOrderId]);

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new ShippingOrderArgs(self::EVENT_PREFIX . 'created', $shippingOrder));

        return $shippingOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function read($shippingOrderId)
    {
        /** @var ShippingOrderModel $shippingOrder */
        /** @noinspection PhpUndefinedMethodInspection */
        $shippingOrder = $this->shippingOrderModel->newQuery()->withCarrierAndStatus()->findOrFail($shippingOrderId);

        Permissions::check($shippingOrder, Permission::view());

        return $shippingOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function update($shippingOrderId, array $input)
    {
        // we allow changing only shipping status
        $shippingStatusCode = S\array_get_value($input, self::PARAM_STATUS_CODE);
        !is_null($shippingStatusCode) ?: S\throwEx(new InvalidArgumentException(self::PARAM_STATUS_CODE));

        /** @var ShippingOrderModel $shippingOrder */
        $shippingOrder = $this->shippingOrderModel->newQuery()->findOrFail($shippingOrderId);
        Permissions::check($shippingOrder, Permission::edit());

        $shippingOrder->{ShippingOrderStatusModel::FIELD_ID} = $this->shippingOrderStatusModel
            ->selectByCode($shippingStatusCode)->firstOrFail([ShippingOrderStatusModel::FIELD_ID])
            ->{ShippingOrderStatusModel::FIELD_ID};

        $shippingOrder->save() ?: S\throwEx(new ValidationException($shippingOrder->getValidator()));

        Event::fire(new ShippingOrderArgs(self::EVENT_PREFIX . 'updated', $shippingOrder));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($shippingOrderId)
    {
        /** @var ShippingOrderModel $shippingOrder */
        $shippingOrder = $this->shippingOrderModel->newQuery()->findOrFail($shippingOrderId);

        Permissions::check($shippingOrder, Permission::delete());

        $shippingOrder->deleteOrFail();

        Event::fire(new ShippingOrderArgs(self::EVENT_PREFIX . 'deleted', $shippingOrder));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->shippingOrderModel->newQuery()->withCarrierAndStatus();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $shippingOrders = $builder->get();

        foreach ($shippingOrders as $shippingOrder) {
            /** @var ShippingOrderModel $shippingOrder */
            Permissions::check($shippingOrder, Permission::view());
        }

        return $shippingOrders;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCosts(ShippingData $shippingData, Carrier $carrier)
    {
        /** @var CarriersInterface $carriers */
        /** @noinspection PhpUndefinedMethodInspection */
        $carriers = App::make(Carriers::INTERFACE_BIND_NAME);
        return $carriers->calculateTariff($shippingData, $carrier);
    }
}
