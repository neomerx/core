<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\CustomerAddress;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Addresses\AddressesInterface;

interface CustomerAddressesInterface
{
    const PARAM_ADDRESS1           = AddressesInterface::PARAM_ADDRESS1;
    const PARAM_ADDRESS2           = AddressesInterface::PARAM_ADDRESS2;
    const PARAM_CITY               = AddressesInterface::PARAM_CITY;
    const PARAM_POSTCODE           = AddressesInterface::PARAM_POSTCODE;
    const PARAM_REGION_CODE        = AddressesInterface::PARAM_REGION_CODE;
    const PARAM_ADDRESS_TYPE       = CustomerAddress::FIELD_TYPE;
    const PARAM_ADDRESS_IS_DEFAULT = CustomerAddress::FIELD_IS_DEFAULT;

    /**
     * Get customer's addresses.
     *
     * @param Customer $customer
     *
     * @return Collection
     */
    public function getAddresses(Customer $customer);

    /**
     * Get intermediate objects between customer and addresses (CustomerAddress).
     *
     * @param Customer $customer
     *
     * @return Collection
     */
    public function getCustomerAddresses(Customer $customer);

    /**
     * Get customer's address.
     *
     * @param Customer $customer
     * @param int      $addressId
     *
     * @return Address
     */
    public function getAddress(Customer $customer, $addressId);

    /**
     * Create customer address.
     *
     * @param Customer $customer
     * @param array    $input
     *
     * @return Address
     */
    public function createAddress(Customer $customer, array $input);

    /**
     * Delete customer's address.
     *
     * @param Customer $customer
     * @param Address  $address
     * @param string   $type
     *
     * @return void
     */
    public function deleteAddress(Customer $customer, Address $address, $type);

    /**
     * Set customer's address as default (for billing or shipping).
     *
     * @param Customer $customer
     * @param Address  $address
     * @param string   $type
     *
     * @return void
     */
    public function setDefaultAddress(Customer $customer, Address $address, $type);
}
