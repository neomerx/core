<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface OrdersInterface extends CrudInterface
{
    const PARAM_ADDRESSES              = 'addresses';
    const PARAM_ADDRESSES_BILLING      = Order::FIELD_BILLING_ADDRESS;
    const PARAM_ADDRESSES_SHIPPING     = Order::FIELD_SHIPPING_ADDRESS;
    const PARAM_ADDRESS_TYPE           = 'address_type';
    const PARAM_ADDRESS_TYPE_NEW       = 'new';
    const PARAM_ADDRESS_TYPE_EXISTING  = 'existing';
    const PARAM_ADDRESS_ID             = 'id';

    const PARAM_CUSTOMER               = Order::FIELD_CUSTOMER;
    const PARAM_CUSTOMER_TYPE          = 'customer_type';
    const PARAM_CUSTOMER_TYPE_NEW      = 'new';
    const PARAM_CUSTOMER_TYPE_EXISTING = 'existing';
    const PARAM_CUSTOMER_ID            = 'id';

    const PARAM_ORDER_DETAILS          = Order::FIELD_DETAILS;
    const PARAM_ORDER_DETAILS_SKU      = 'sku';
    const PARAM_STORE_CODE             = 'store_code';
    const PARAM_ORDER_DETAILS_QUANTITY = 'quantity';

    const PARAM_ORDER_STATUS_CODE      = 'order_status_code';

    const PARAM_SHIPPING               = 'shipping';
    const PARAM_SHIPPING_TYPE          = 'type';        // delivery or pickup
    const PARAM_SHIPPING_TYPE_DELIVERY = 'delivery';
    const PARAM_SHIPPING_TYPE_PICKUP   = 'pickup';
    const PARAM_SHIPPING_CARRIER_CODE  = 'carrier_code';// for delivery
    const PARAM_SHIPPING_PLACE_CODE    = 'place_code';  // for pickup

    const EVENT_PREFIX = 'Api.Order.';

    /**
     * Create order.
     *
     * @param array $input
     *
     * @return Order
     */
    public function create(array $input);

    /**
     * Read order by identifier.
     *
     * @param string $code
     *
     * @return Order
     */
    public function read($code);

    /**
     * Search orders.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
