<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Api\Cart\Cart;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Exceptions\LogicException;

class ShippingData
{
    const TYPE_PICKUP   = 1;
    const TYPE_DELIVERY = 2;

    /**
     * @var Address
     */
    private $addressTo;

    /**
     * @var Store
     */
    private $pickupStore;

    /**
     * @var Address
     */
    private $addressFrom;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @param Customer $customer
     * @param Cart     $cart
     * @param Address  $addressTo
     * @param Store    $pickupStore
     * @param Address  $addressFrom
     */
    public function __construct(
        Customer $customer,
        Cart $cart,
        Address $addressTo = null,
        Store $pickupStore = null,
        Address $addressFrom = null
    ) {
        // one and only one of the variables should be set either pickup store or shipping address
        ($addressTo === null xor $pickupStore === null) ?: S\throwEx(new LogicException());

        $this->cart        = $cart;
        $this->customer    = $customer;
        $this->addressTo   = $addressTo;
        $this->pickupStore = $pickupStore;
        $this->addressFrom = $addressFrom;
    }

    /**
     * @return \Neomerx\Core\Models\Address
     */
    public function getAddressTo()
    {
        return $this->addressTo;
    }

    /**
     * @return \Neomerx\Core\Models\Store
     */
    public function getPickupStore()
    {
        return $this->pickupStore;
    }

    /**
     * @return \Neomerx\Core\Models\Address
     */
    public function getAddressFrom()
    {
        return $this->addressFrom;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @return \Neomerx\Core\Models\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @see ShippingData::TYPE_PICKUP and ShippingData::TYPE_DELIVERY
     *
     * @return int
     */
    public function getShippingType()
    {
        return $this->pickupStore ? self::TYPE_PICKUP : self::TYPE_DELIVERY;
    }
}
