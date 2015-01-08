<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Api\Addresses\Addresses;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Addresses\AddressArgs;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAddresses implements CustomerAddressesInterface
{
    const EVENT_PREFIX = 'Api.Address.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Customer
     */
    private $customerModel;

    /**
     * @var Addresses
     */
    private $addressApi;

    /**
     * @var CustomerAddress
     */
    private $customerAddressModel;

    /**
     * @var Region
     */
    private $regionModel;

    /**
     * @param Customer        $customer
     * @param Addresses       $addressApi
     * @param CustomerAddress $customerAddress
     * @param Region          $region
     */
    public function __construct(
        Customer $customer,
        Addresses $addressApi,
        CustomerAddress $customerAddress,
        Region $region
    ) {
        $this->customerModel        = $customer;
        $this->addressApi           = $addressApi;
        $this->customerAddressModel = $customerAddress;
        $this->regionModel          = $region;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses(Customer $customer)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $addresses = $customer->addresses()->withRegionAndCountry()->get();

        /** @var \Neomerx\Core\Models\Address $address */
        foreach ($addresses as $address) {
            Permissions::check($address, Permission::view());
        }

        return $addresses;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerAddresses(Customer $customer)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $customerAddresses = $customer->customerAddresses()->get();

        /** @var \Neomerx\Core\Models\CustomerAddress $customerAddress */
        foreach ($customerAddresses as $customerAddress) {
            Permissions::check($customerAddress, Permission::view());
        }

        return $customerAddresses;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(Customer $customer, $addressId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $address = $customer->addresses()
            ->wherePivot(CustomerAddress::FIELD_ID_ADDRESS, $addressId)
            ->withRegionAndCountry()
            ->firstOrFail();

        Permissions::check($address, Permission::view());

        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function createAddress(Customer $customer, array $input)
    {
        Permissions::check($customer, Permission::edit());

        $addressType = S\array_get_value($input, self::PARAM_ADDRESS_TYPE);
        isset($addressType) ?: S\throwEx(new InvalidArgumentException(self::PARAM_ADDRESS_TYPE));
        settype($addressType, 'string');

        $isDefault = S\array_get_value($input, self::PARAM_ADDRESS_IS_DEFAULT);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create address
            /** @var \Neomerx\Core\Models\Address $address */
            $address = $this->addressApi->create($input);

            // create link between address and customer
            $this->customerAddressModel->createOrFailResource([
                CustomerAddress::FIELD_ID_CUSTOMER => $customer->{Customer::FIELD_ID},
                CustomerAddress::FIELD_ID_ADDRESS  => $address->{Address::FIELD_ID},
                CustomerAddress::FIELD_TYPE        => $addressType,
            ]);

            // mark address as default if necessary
            if ($isDefault == true) {
                $this->setDefaultAddressImpl(
                    $customer->{Customer::FIELD_ID},
                    $address->{Address::FIELD_ID},
                    $addressType
                );
            }

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        $addressArgs = new AddressArgs(self::EVENT_PREFIX . 'created', $address);
        Event::fire(new CustomerArgs(Customers::EVENT_PREFIX . 'updated', $customer, $addressArgs));

        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAddress(Customer $customer, Address $address, $type)
    {
        $customerAddress = $this->getCustomerAndAddressLink(
            $customer->{Customer::FIELD_ID},
            $address->{Address::FIELD_ID},
            $type
        );

        Permissions::check($customer, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // delete customer_address record
            $customerAddress->deleteOrFail();
            $this->addressApi->deleteModel($address);

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        $addressDeletedArgs = new AddressArgs(self::EVENT_PREFIX . 'deleted', $address);
        Event::fire(new CustomerArgs(Customers::EVENT_PREFIX . 'updated', $customer, $addressDeletedArgs));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultAddress(Customer $customer, Address $address, $type)
    {
        Permissions::check($customer, Permission::edit());

        $this->setDefaultAddressImpl($customer->{Customer::FIELD_ID}, $address->{Address::FIELD_ID}, $type);

        $addressArgs = new AddressArgs(self::EVENT_PREFIX . 'updated', $address);
        Event::fire(new CustomerArgs(Customers::EVENT_PREFIX . 'updated', $customer, $addressArgs));
    }

    /**
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     */
    private function setDefaultAddressImpl($customerId, $addressId, $type)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $customerAddress = $this->getCustomerAndAddressLink($customerId, $addressId, $type);

            /** @noinspection PhpUndefinedMethodInspection */
            $this->customerAddressModel
                ->where(CustomerAddress::FIELD_ID_CUSTOMER, '=', $customerId)
                ->where(CustomerAddress::FIELD_TYPE, '=', $type)
                ->update([CustomerAddress::FIELD_IS_DEFAULT => null]);

            /** @noinspection PhpUndefinedMethodInspection */
            $this->customerAddressModel
                ->where(CustomerAddress::FIELD_ID, '=', $customerAddress->{CustomerAddress::FIELD_ID})
                ->update([CustomerAddress::FIELD_IS_DEFAULT => true]);

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }
    }

    /**
     * @param int    $customerId
     * @param int    $addressId
     * @param string $type
     *
     * @return \Neomerx\Core\Models\CustomerAddress
     */
    private function getCustomerAndAddressLink($customerId, $addressId, $type)
    {
        /** @var \Neomerx\Core\Models\CustomerAddress $customerAddress */
        /** @noinspection PhpUndefinedMethodInspection */
        $customerAddress = $this->customerAddressModel->where([
            CustomerAddress::FIELD_ID_CUSTOMER => $customerId,
            CustomerAddress::FIELD_ID_ADDRESS  => $addressId,
            CustomerAddress::FIELD_TYPE        => $type,
        ])->firstOrFail();

        return $customerAddress;
    }
}
