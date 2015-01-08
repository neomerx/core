<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Api\Facades\Stores;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Api\Orders\CreateTrait;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Taxes\TaxesInterface;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Neomerx\Core\Api\Orders\ParseInputTrait;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Customers\CustomersInterface;
use \Neomerx\Core\Api\Inventory\InventoriesInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\ShippingOrders\ShippingOrdersInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Orders implements OrdersInterface
{
    use CreateTrait;
    use ParseInputTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var Order
     */
    private $orderModel;

    /**
     * @var CustomersInterface
     */
    private $customersApi;

    /**
     * @var ShippingOrdersInterface
     */
    private $shippingOrdersApi;

    /**
     * @var InventoriesInterface
     */
    private $inventoryApi;

    /**
     * @var TaxesInterface
     */
    private $taxesApi;

    /**
     * @var OrderStatus
     */
    private $orderStatusModel;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @var Warehouse
     */
    private $warehouseModel;

    protected static $orderRelations = [
        'shippingAddress.region.country',
        'billingAddress.region.country',
        'store.address.region.country',
        Order::FIELD_STATUS,
        'details.variant',
        'details',
        'shippingOrders.status'
    ];

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        'created'                 => [SearchGrammar::TYPE_DATE, Order::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Order::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param CustomersInterface      $customers
     * @param InventoriesInterface      $inventory
     * @param Order                   $order
     * @param OrderStatus             $orderStatus
     * @param ShippingOrdersInterface $shippingOrders
     * @param TaxesInterface          $taxes
     * @param Warehouse               $warehouse
     * @param Variant                 $variant
     */
    public function __construct(
        CustomersInterface $customers,
        InventoriesInterface $inventory,
        Order $order,
        OrderStatus $orderStatus,
        ShippingOrdersInterface $shippingOrders,
        TaxesInterface $taxes,
        Warehouse $warehouse,
        Variant $variant
    ) {
        $this->orderModel        = $order;
        $this->orderStatusModel  = $orderStatus;
        $this->customersApi      = $customers;
        $this->inventoryApi      = $inventory;
        $this->shippingOrdersApi = $shippingOrders;
        $this->taxesApi          = $taxes;
        $this->warehouseModel    = $warehouse;
        $this->variantModel      = $variant;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create(array $input)
    {
        /** @var \Neomerx\Core\Api\Cart\Cart $cart */
        /** @var \Neomerx\Core\Models\Store $store */
        /** @var \Neomerx\Core\Models\Customer $customer */

        list($carrier, $store,
            $isNewCustomer, $customerData, $customer,
            $isNewBilling, $billingAddressData, $billingAddressId,
            $isNewShipping, $shippingAddressData, $shippingAddressId,
            $cart) = $this->parseInput($input);

        /** @var Order $order */
        $order = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create customer if required
            ($isNewCustomer === null or (empty($customerData) and $customer === null)) ?
                S\throwEx(new InvalidArgumentException(self::PARAM_CUSTOMER)) : null;
            if ($isNewCustomer) {
                $customer = $this->customersApi->create($customerData);
            }

            // create or find addresses
            list($billingAddress, $shippingAddress) = $this->createOrFindAddresses(
                $this->customersApi,
                $customer,
                $isNewBilling,
                $billingAddressData,
                $billingAddressId,
                $isNewShipping,
                $shippingAddressData,
                $shippingAddressId
            );
            unset($billingAddressId);
            unset($shippingAddressId);

            $addressFrom = Stores::getDefault()->address;
            $shippingData = new ShippingData($customer, $cart, $shippingAddress, $store, $addressFrom);

            // sanity check: shipping address shouldn't be specified with pickup
            (ShippingData::TYPE_PICKUP === $shippingData->getShippingType() and $shippingAddress !== null) ?
                S\throwEx(new InvalidArgumentException(self::PARAM_SHIPPING_TYPE_PICKUP)) : null;

            // calculate shipping costs. We assume it costs nothing for pickup. If not change this one -------|
            $tariff = (ShippingData::TYPE_PICKUP === $shippingData->getShippingType() ? new Tariff(0) : // <--
                $this->shippingOrdersApi->calculateCosts($shippingData, $carrier));

            // calculate taxes for shipping address which could be store (pickup place) or client's shipping address
            $taxCalculation = $this->taxesApi->calculateTax($shippingData, $tariff);

            // create an order
            $order = $this->createNewOrderWithDetails(
                $shippingData,
                $billingAddress,
                $taxCalculation,
                $tariff,
                $this->orderStatusModel,
                $this->inventoryApi
            );

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new OrderArgs(self::EVENT_PREFIX . 'created', $order));

        return $order;
    }

    /**
     * @inheritdoc
     */
    public function read($orderId)
    {
        /** @var \Neomerx\Core\Models\Order $order */
        $order = $this->orderModel->with(static::$orderRelations)->findOrFail($orderId);

        Permissions::check($order, Permission::view());

        return $order;
    }

    /**
     * @inheritdoc
     */
    public function update($orderId, array $input)
    {
        // we do not allow changing order details or shipping address but order status could be changed.
        $orderStatusCode = S\array_get_value($input, self::PARAM_ORDER_STATUS_CODE);
        !is_null($orderStatusCode) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ORDER_STATUS_CODE));

        /** @var \Neomerx\Core\Models\Order $order */
        $order = $this->orderModel->findOrFail($orderId);
        Permissions::check($order, Permission::edit());

        /** @var \Neomerx\Core\Models\OrderStatus $availableStatus */
        foreach ($order->status->available_statuses as $availableStatus) {
            if ($availableStatus->code === $orderStatusCode) {
                $order->{OrderStatus::FIELD_ID} = $availableStatus->{OrderStatus::FIELD_ID};
                $order->save() ?: S\throwEx(new ValidationException($order->getValidator()));
                $statusFound = true;

                Event::fire(new OrderArgs(self::EVENT_PREFIX . 'updated', $order));

                break;
            }
        }

        isset($statusFound) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ORDER_STATUS_CODE));
    }

    /**
     * @inheritdoc
     */
    public function delete($orderId)
    {
        /** @var \Neomerx\Core\Models\Order $order */
        $order = $this->orderModel->findOrFail($orderId);
        Permissions::check($order, Permission::delete());
        $order->deleteOrFail();

        Event::fire(new OrderArgs(self::EVENT_PREFIX . 'deleted', $order));
    }

    /**
     * @inheritdoc
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpParamsInspection */
        $builder = $this->orderModel->newQuery()->with(static::$orderRelations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $orders = $builder->get();

        foreach ($orders as $order) {
            /** @var \Neomerx\Core\Models\Order $order */
            Permissions::check($order, Permission::view());
        }

        return $orders;
    }
}
