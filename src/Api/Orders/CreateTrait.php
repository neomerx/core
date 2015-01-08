<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Api\Customers;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Cart\CartItem;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Api\Facades\Warehouses;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Api\Taxes\TaxCalculation;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Inventory\InventoriesInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Customers\CustomerAddressesInterface;

trait CreateTrait
{
    /**
     * @param ShippingData       $shippingData
     * @param Address            $billingAddress
     * @param TaxCalculation     $taxCalculation
     * @param Tariff             $shippingTariff
     * @param OrderStatus        $orderStatusModel
     * @param InventoriesInterface $inventoryApi
     *
     * @return Order
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    private function createNewOrderWithDetails(
        ShippingData $shippingData,
        Address $billingAddress,
        TaxCalculation $taxCalculation,
        Tariff $shippingTariff,
        OrderStatus $orderStatusModel,
        InventoriesInterface $inventoryApi
    ) {
        $newOrderStatusId = $orderStatusModel->selectByCode(OrderStatus::STATUS_NEW_ORDER)
            ->firstOrFail([OrderStatus::FIELD_ID])->{OrderStatus::FIELD_ID};

        $shipAddressId = $shippingData->getAddressTo()   ? $shippingData->getAddressTo()->{Address::FIELD_ID} : null;
        $storeId       = $shippingData->getPickupStore() ? $shippingData->getPickupStore()->{Store::FIELD_ID} : null;
        $orderData = [
            Order::FIELD_ID_CUSTOMER           => $shippingData->getCustomer()->{Customer::FIELD_ID},
            Order::FIELD_ID_BILLING_ADDRESS    => $billingAddress->{Address::FIELD_ID},
            Order::FIELD_ID_SHIPPING_ADDRESS   => $shipAddressId,
            Order::FIELD_ID_STORE              => $storeId,
            Order::FIELD_ID_ORDER_STATUS       => $newOrderStatusId,
            Order::FIELD_PRODUCTS_TAX          => $taxCalculation->getTax(),
            Order::FIELD_SHIPPING_INCLUDED_TAX => $taxCalculation->getShippingCost(),
            Order::FIELD_SHIPPING_COST         => $shippingTariff->getCost(),
        ];

        $orderData = S\array_filter_nulls($orderData);

        /** @var \Neomerx\Core\Models\Order $order */
        /** @noinspection PhpUndefinedMethodInspection */
        $order = App::make(Order::BIND_NAME);
        $order->fill($orderData);
        $order->{Order::FIELD_PRODUCTS_TAX_DETAILS} = json_encode($taxCalculation->getDetails());
        $order->saveOrFail();
        Permissions::check($order, Permission::create());

        // order details will be reserved at this warehouse
        $reserveAtWarehouse = Warehouses::getDefault();

        /** @var CartItem $cartItem */
        foreach ($shippingData->getCart() as $cartItem) {

            /** @var \Neomerx\Core\Models\Variant $item */
            $item = $cartItem->getVariant();

            $inventoryApi->makeReserve($item, $reserveAtWarehouse, $cartItem->getQuantity());

            /** @var OrderDetails $orderDetails */
            /** @noinspection PhpUndefinedMethodInspection */
            $orderDetails = App::make(OrderDetails::BIND_NAME);
            $orderDetails->fill([
                OrderDetails::FIELD_ID_VARIANT   => $item->getKey(),
                OrderDetails::FIELD_PRICE_WO_TAX => $item->price_wo_tax,
                OrderDetails::FIELD_QUANTITY     => $cartItem->getQuantity(),
            ]);
            $saved = $order->details()->save($orderDetails);
            ($saved and $saved->exists) ?: S\throwEx(new ValidationException($orderDetails->getValidator()));
        }

        return $order;
    }

    /**
     * @param CustomerAddressesInterface $customerAddressesApi
     * @param Customer                   $customer
     * @param bool                       $isNewBilling
     * @param array                      $billingAddressData
     * @param int                        $billingAddressId
     * @param bool                       $isNewShipping
     * @param array                      $shippingAddressData
     * @param int                        $shippingAddressId
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    private function createOrFindAddresses(
        CustomerAddressesInterface $customerAddressesApi,
        Customer $customer,
        $isNewBilling,
        $billingAddressData,
        $billingAddressId,
        $isNewShipping,
        $shippingAddressData,
        $shippingAddressId
    ) {
        // create or find billing address
        $billingAddress = $this->createOrFindAddress(
            $customerAddressesApi,
            $customer,
            $isNewBilling,
            $billingAddressData,
            $billingAddressId,
            CustomerAddress::TYPE_BILLING,
            Orders::PARAM_ADDRESSES_BILLING
        );

        // create or find shipping address
        $shippingAddress = $this->createOrFindAddress(
            $customerAddressesApi,
            $customer,
            $isNewShipping,
            $shippingAddressData,
            $shippingAddressId,
            CustomerAddress::TYPE_SHIPPING,
            Orders::PARAM_ADDRESSES_SHIPPING
        );

        return [$billingAddress, $shippingAddress];
    }

    /**
     * @param CustomerAddressesInterface $api
     * @param Customer                   $customer
     * @param bool                       $isNew
     * @param array|null                 $addressData
     * @param int                        $addressId
     * @param string                     $addressType
     * @param string                     $invalidArgExMsg
     *
     * @return Address|null
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function createOrFindAddress(
        CustomerAddressesInterface $api,
        Customer $customer,
        $isNew,
        $addressData,
        $addressId,
        $addressType,
        $invalidArgExMsg
    ) {
        if ($isNew === true and !empty($addressData) and $addressId === null) {

            $address = $api->createAddress($customer, array_merge($addressData, [
                CustomerAddress::FIELD_TYPE => $addressType
            ]));

        } elseif ($isNew === false and empty($addressData) and $addressId !== null) {

            $address = $api->getAddress($customer, $addressId);

        } elseif ($isNew === null and empty($addressData) and $addressId === null) {

            $address = null;

        } else {

            throw new InvalidArgumentException($invalidArgExMsg);

        }

        return $address;
    }
}
