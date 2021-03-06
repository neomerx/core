<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CustomerAddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Customer $customer
     * @param Address  $address
     * @param array    $attributes
     *
     * @return CustomerAddress
     */
    public function createWithObjects(Customer $customer, Address $address, array $attributes);

    /**
     * @param int   $customerId
     * @param int   $addressId
     * @param array $attributes
     *
     * @return CustomerAddress
     */
    public function create($customerId, $addressId, array $attributes);

    /**
     * @param CustomerAddress $resource
     * @param Customer|null   $customer
     * @param Address|null    $address
     * @param array|null      $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CustomerAddress $resource,
        Customer $customer = null,
        Address $address = null,
        array $attributes = null
    );

    /**
     * @param CustomerAddress $resource
     * @param int|null        $customerId
     * @param int|null        $addressId
     * @param array|null      $attributes
     *
     * @return void
     */
    public function update(
        CustomerAddress $resource,
        $customerId = null,
        $addressId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return CustomerAddress
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);

    /**
     * @param CustomerAddress $customerAddress
     *
     * @return void
     */
    public function setAsDefault(CustomerAddress $customerAddress);
}
