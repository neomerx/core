<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class CustomerAddressRepository extends IndexBasedResourceRepository implements CustomerAddressRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CustomerAddress::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Customer $customer, Address $address, array $attributes)
    {
        /** @var CustomerAddress $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $customer, $address, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CustomerAddress $resource,
        Customer $customer = null,
        Address $address = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            CustomerAddress::FIELD_ID_ADDRESS  => $address,
            CustomerAddress::FIELD_ID_CUSTOMER => $customer,
        ], $attributes);
    }
}
