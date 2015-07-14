<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Customer     $customer
     * @param OrderStatus  $status
     * @param Currency     $currency
     * @param array        $attributes
     * @param Address|null $shippingAddress
     * @param Address|null $billingAddress
     * @param Store|null   $store
     *
     * @return Order
     */
    public function instance(
        Customer $customer,
        OrderStatus $status,
        Currency $currency,
        array $attributes,
        Address $shippingAddress = null,
        Address $billingAddress = null,
        Store $store = null
    );

    /**
     * @param Order            $resource
     * @param Customer|null    $customer
     * @param OrderStatus|null $status
     * @param Currency|null    $currency
     * @param array|null       $attributes
     * @param Address|null     $shippingAddress
     * @param Address|null     $billingAddress
     * @param Store|null       $store
     *
     */
    public function fill(
        Order $resource,
        Customer $customer = null,
        OrderStatus $status = null,
        Currency $currency = null,
        array $attributes = null,
        Address $shippingAddress = null,
        Address $billingAddress = null,
        Store $store = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Order
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
