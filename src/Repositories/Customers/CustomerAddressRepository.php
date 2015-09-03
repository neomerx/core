<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CustomerAddressRepository extends BaseRepository implements CustomerAddressRepositoryInterface
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
    public function createWithObjects(Customer $customer, Address $address, array $attributes)
    {
        return $this->create($this->idOf($customer), $this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($customerId, $addressId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($customerId, $addressId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        CustomerAddress $resource,
        Customer $customer = null,
        Address $address = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($customer), $this->idOf($address), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        CustomerAddress $resource,
        $customerId = null,
        $addressId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($customerId, $addressId));
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

        $this->executeInTransaction(function () use ($allAddressQry, $customerAddressQry) {
            $allAddressQry->update([CustomerAddress::FIELD_IS_DEFAULT => false]);
            $customerAddressQry->update([CustomerAddress::FIELD_IS_DEFAULT => true]);
        });
    }

    /**
     * @param int $customerId
     * @param int $addressId
     *
     * @return array
     */
    protected function getRelationships($customerId, $addressId)
    {
        return $this->filterNulls([
            CustomerAddress::FIELD_ID_ADDRESS  => $addressId,
            CustomerAddress::FIELD_ID_CUSTOMER => $customerId,
        ]);
    }
}
