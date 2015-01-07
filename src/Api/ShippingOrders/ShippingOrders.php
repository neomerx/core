<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Api\Facades\Carriers;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Neomerx\Core\Models\ShippingOrderStatus;
use \Neomerx\Core\Api\Carriers\CarriersInterface;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingOrders implements ShippingOrdersInterface
{
    const EVENT_PREFIX = 'Api.ShippingOrder.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Order
     */
    private $orderModel;

    /**
     * @var ShippingOrder
     */
    private $shippingOrderModel;

    /**
     * @var ShippingOrderStatus
     */
    private $shippingOrderStatusModel;

    /**
     * @var Carrier
     */
    private $carrierModel;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        ShippingOrder::FIELD_ID_ORDER        => SearchGrammar::TYPE_INT,
        ShippingOrder::FIELD_TRACKING_NUMBER => SearchGrammar::TYPE_STRING,
        'created'                            => [SearchGrammar::TYPE_DATE, ShippingOrder::FIELD_CREATED_AT],
        'updated'                            => [SearchGrammar::TYPE_DATE, ShippingOrder::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP            => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE            => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Order               $order
     * @param Carrier             $carrier
     * @param ShippingOrder       $shippingOrder
     * @param ShippingOrderStatus $shippingOrderStatus
     */
    public function __construct(
        Order $order,
        Carrier $carrier,
        ShippingOrder $shippingOrder,
        ShippingOrderStatus $shippingOrderStatus
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
        $orderId             =  S\array_get_value($input, Order::FIELD_ID);
        $orderId            !== null ?: S\throwEx(new InvalidArgumentException(Order::FIELD_ID));

        $carrierCode         =  S\array_get_value($input, self::PARAM_CARRIER_CODE);
        $carrierCode        !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_CARRIER_CODE));

        $trackingNumber      =  S\array_get_value($input, self::PARAM_TRACKING_NUMBER);
        $trackingNumber     !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_TRACKING_NUMBER));

        $shippingStatusCode  = S\array_get_value($input, self::PARAM_STATUS_CODE);
        $shippingStatusCode !== null ?: S\throwEx(new InvalidArgumentException(self::PARAM_STATUS_CODE));

        // if null then all inventory for the order would be added to shipping order
        $detailsIds =  S\array_get_value($input, self::PARAM_DETAIL_IDS);

        $statusId = $this->shippingOrderStatusModel->selectByCode($shippingStatusCode)
            ->firstOrFail([ShippingOrderStatus::FIELD_ID])->{ShippingOrderStatus::FIELD_ID};

        /** @var Order $order */
        $order = $this->orderModel->findOrFail($orderId);
        Permissions::check($order, Permission::edit());

        /** @var Carrier $carrier */
        $carrier = $this->carrierModel->selectByCode($carrierCode)->firstOrFail([Carrier::FIELD_ID]);
        Permissions::check($carrier, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var ShippingOrder $shippingOrder */
            $shippingOrder = $this->shippingOrderModel->createOrFailResource([
                ShippingOrder::FIELD_ID_ORDER                 => $orderId,
                ShippingOrder::FIELD_ID_CARRIER               => $carrier->{Carrier::FIELD_ID},
                ShippingOrder::FIELD_ID_SHIPPING_ORDER_STATUS => $statusId,
                ShippingOrder::FIELD_TRACKING_NUMBER          => $trackingNumber,
            ]);
            Permissions::check($shippingOrder, Permission::create());
            $shippingOrderId = $shippingOrder->{ShippingOrder::FIELD_ID};

            // update order details with shipping order info
            /** @noinspection PhpUndefinedMethodInspection */
            $detailsRelation = $order->details()->whereNull(OrderDetails::FIELD_ID_SHIPPING_ORDER);

            // if specific order details IDs were specified will add it to the filter
            if (!empty($detailsIds)) {
                /** @noinspection PhpUndefinedMethodInspection */
                $detailsRelation = $detailsRelation->whereIn(OrderDetails::FIELD_ID, $detailsIds);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $detailsRelation->update([ShippingOrder::FIELD_ID => $shippingOrderId]);

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
        /** @var ShippingOrder $shippingOrder */
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

        /** @var ShippingOrder $shippingOrder */
        $shippingOrder = $this->shippingOrderModel->newQuery()->findOrFail($shippingOrderId);
        Permissions::check($shippingOrder, Permission::edit());

        $shippingOrder->{ShippingOrderStatus::FIELD_ID} = $this->shippingOrderStatusModel
            ->selectByCode($shippingStatusCode)->firstOrFail([ShippingOrderStatus::FIELD_ID])
            ->{ShippingOrderStatus::FIELD_ID};

        $shippingOrder->save() ?: S\throwEx(new ValidationException($shippingOrder->getValidator()));

        Event::fire(new ShippingOrderArgs(self::EVENT_PREFIX . 'updated', $shippingOrder));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($shippingOrderId)
    {
        /** @var ShippingOrder $shippingOrder */
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
            /** @var ShippingOrder $shippingOrder */
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
