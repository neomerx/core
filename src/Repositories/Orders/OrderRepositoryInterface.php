<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Customer      $customer
     * @param OrderStatus   $status
     * @param Currency      $currency
     * @param array         $attributes
     * @param Nullable|null $shippingAddress Address
     * @param Nullable|null $billingAddress  Address
     * @param Nullable|null $store           Store
     *
     * @return Order
     */
    public function createWithObjects(
        Customer $customer,
        OrderStatus $status,
        Currency $currency,
        array $attributes,
        Nullable $shippingAddress = null,
        Nullable $billingAddress = null,
        Nullable $store = null
    );

    /**
     * @param int           $customerId
     * @param int           $statusId
     * @param int           $currencyId
     * @param array         $attributes
     * @param Nullable|null $shippingAddressId
     * @param Nullable|null $billingAddressId
     * @param Nullable|null $storeId
     *
     * @return Order
     */
    public function create(
        $customerId,
        $statusId,
        $currencyId,
        array $attributes,
        Nullable $shippingAddressId = null,
        Nullable $billingAddressId = null,
        Nullable $storeId = null
    );

    /**
     * @param Order            $resource
     * @param Customer|null    $customer
     * @param OrderStatus|null $status
     * @param Currency|null    $currency
     * @param array|null       $attributes
     * @param Nullable|null    $shippingAddress Address
     * @param Nullable|null    $billingAddress  Address
     * @param Nullable|null    $store           Store
     *
     * @return void
     */
    public function updateWithObjects(
        Order $resource,
        Customer $customer = null,
        OrderStatus $status = null,
        Currency $currency = null,
        array $attributes = null,
        Nullable $shippingAddress = null,
        Nullable $billingAddress = null,
        Nullable $store = null
    );

    /**
     * @param Order         $resource
     * @param int|null      $customerId
     * @param int|null      $statusId
     * @param int|null      $currencyId
     * @param array|null    $attributes
     * @param Nullable|null $shippingAddressId
     * @param Nullable|null $billingAddressId
     * @param Nullable|null $storeId
     *
     * @return void
     */
    public function update(
        Order $resource,
        $customerId = null,
        $statusId = null,
        $currencyId = null,
        array $attributes = null,
        Nullable $shippingAddressId = null,
        Nullable $billingAddressId = null,
        Nullable $storeId = null
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
