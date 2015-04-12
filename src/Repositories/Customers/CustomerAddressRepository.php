<?php namespace Neomerx\Core\Repositories\Customers;

use \DB;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CustomerAddressRepository extends IndexBasedResourceRepository implements CustomerAddressRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CustomerAddress::class);
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

    /**
     * @inheritdoc
     */
    public function setAsDefault(CustomerAddress $customerAddress)
    {
        $customerAddressModel = $this->getUnderlyingModel();

        $allAddressQry = $customerAddressModel->where([
            CustomerAddress::FIELD_ID_CUSTOMER => $customerAddress->{CustomerAddress::FIELD_ID_CUSTOMER},
            CustomerAddress::FIELD_TYPE        => $customerAddress->{CustomerAddress::FIELD_TYPE},
        ]);

        $customerAddressQry = $customerAddressModel->where([
            CustomerAddress::FIELD_ID => $customerAddress->{CustomerAddress::FIELD_ID}
        ]);

        DB::beginTransaction();
        try {
            $allAddressQry->update([CustomerAddress::FIELD_IS_DEFAULT => false]);
            $customerAddressQry->update([CustomerAddress::FIELD_IS_DEFAULT => true]);

            $allExecutedOk = true;
        } finally {
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }
}
