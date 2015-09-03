<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Order::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(
        Customer $customer,
        OrderStatus $status,
        Currency $currency,
        array $attributes,
        Nullable $shippingAddress = null,
        Nullable $billingAddress = null,
        Nullable $store = null
    ) {
        return $this->create(
            $this->idOf($customer),
            $this->idOf($status),
            $this->idOf($currency),
            $attributes,
            $this->idOfNullable($shippingAddress, Address::class),
            $this->idOfNullable($billingAddress, Address::class),
            $this->idOfNullable($store, Store::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function create(
        $customerId,
        $statusId,
        $currencyId,
        array $attributes,
        Nullable $shippingAddressId = null,
        Nullable $billingAddressId = null,
        Nullable $storeId = null
    ) {
        $resource = $this->createWith(
            $attributes,
            $this->getRelationships(
                $customerId,
                $statusId,
                $currencyId,
                $shippingAddressId,
                $billingAddressId,
                $storeId
            )
        );

        return $resource;
    }

    /**
     * @inheritdoc
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
    ) {
        $this->update(
            $resource,
            $this->idOf($customer),
            $this->idOf($status),
            $this->idOf($currency),
            $attributes,
            $this->idOfNullable($shippingAddress, Address::class),
            $this->idOfNullable($billingAddress, Address::class),
            $this->idOfNullable($store, Store::class)
        );
    }

    /**
     * @inheritdoc
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
    ) {
        $this->updateWith(
            $resource,
            $attributes,
            $this->getRelationships(
                $customerId,
                $statusId,
                $currencyId,
                $shippingAddressId,
                $billingAddressId,
                $storeId
            )
        );
    }

    /**
     * @param int           $customerId
     * @param int           $statusId
     * @param int           $currencyId
     * @param Nullable|null $shippingAddressId
     * @param Nullable|null $billingAddressId
     * @param Nullable|null $storeId
     *
     * @return array
     */
    protected function getRelationships(
        $customerId,
        $statusId,
        $currencyId,
        Nullable $shippingAddressId = null,
        Nullable $billingAddressId = null,
        Nullable $storeId = null
    ) {
        return $this->filterNulls([
            Order::FIELD_ID_CUSTOMER         => $customerId,
            Order::FIELD_ID_ORDER_STATUS     => $statusId,
            Order::FIELD_ID_CURRENCY         => $currencyId,
        ], [
            Order::FIELD_ID_BILLING_ADDRESS  => $billingAddressId,
            Order::FIELD_ID_SHIPPING_ADDRESS => $shippingAddressId,
            Order::FIELD_ID_STORE            => $storeId,
        ]);
    }
}
