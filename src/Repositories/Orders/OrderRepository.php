<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class OrderRepository extends IndexBasedResourceRepository implements OrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Order::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Customer $customer,
        OrderStatus $status,
        array $attributes,
        Address $shippingAddress = null,
        Address $billingAddress = null,
        Store $store = null
    ) {
        /** @var Order $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $customer, $status, $attributes, $shippingAddress, $billingAddress, $store);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Order $resource,
        Customer $customer = null,
        OrderStatus $status = null,
        array $attributes = null,
        Address $shippingAddress = null,
        Address $billingAddress = null,
        Store $store = null
    ) {
        $this->fillModel($resource, [
            Order::FIELD_ID_CUSTOMER         => $customer,
            Order::FIELD_ID_ORDER_STATUS     => $status,
            Order::FIELD_ID_BILLING_ADDRESS  => $billingAddress,
            Order::FIELD_ID_SHIPPING_ADDRESS => $shippingAddress,
            Order::FIELD_ID_STORE            => $store,
        ], $attributes);
    }
}
