<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\CustomerAddress;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Addresses\AddressArgs;
use \Neomerx\Core\Models\Region as RegionModel;
use \Neomerx\Core\Models\Address as AddressModel;
use \Neomerx\Core\Models\Customer as CustomerModel;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Addresses\Addresses as AddressesApi;
use \Neomerx\Core\Models\CustomerAddress as CustomerAddressModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAddresses implements CustomerAddressesInterface
{
    const EVENT_PREFIX = 'Api.Address.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var CustomerModel
     */
    private $customerModel;

    /**
     * @var AddressesApi
     */
    private $addressApi;

    /**
     * @var CustomerAddressModel
     */
    private $customerAddressModel;

    /**
     * @var RegionModel
     */
    private $regionModel;

    /**
     * @param CustomerModel        $customer
     * @param AddressesApi         $addressApi
     * @param CustomerAddressModel $customerAddress
     * @param RegionModel          $region
     */
    public function __construct(
        CustomerModel $customer,
        AddressesApi $addressApi,
        CustomerAddressModel $customerAddress,
        RegionModel $region
    ) {
        $this->customerModel        = $customer;
        $this->addressApi           = $addressApi;
        $this->customerAddressModel = $customerAddress;
        $this->regionModel          = $region;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses(CustomerModel $customer)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $addresses = $customer->addresses()->withRegionAndCountry()->get();

        /** @var AddressModel $address */
        foreach ($addresses as $address) {
            Permissions::check($address, Permission::view());
        }

        return $addresses;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(CustomerModel $customer, $addressId)
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
    public function createAddress(CustomerModel $customer, array $input)
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
            /** @var AddressModel $address */
            $address = $this->addressApi->create($input);

            // create link between address and customer
            $this->customerAddressModel->createOrFailResource([
                CustomerAddress::FIELD_ID_CUSTOMER => $customer->{CustomerModel::FIELD_ID},
                CustomerAddress::FIELD_ID_ADDRESS  => $address->{AddressModel::FIELD_ID},
                CustomerAddress::FIELD_TYPE        => $addressType,
            ]);

            // mark address as default if necessary
            if ($isDefault == true) {
                $this->setDefaultAddressImpl(
                    $customer->{CustomerModel::FIELD_ID},
                    $address->{AddressModel::FIELD_ID},
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
    public function deleteAddress(CustomerModel $customer, AddressModel $address, $type)
    {
        $customerAddress = $this->getCustomerAndAddressLink(
            $customer->{CustomerModel::FIELD_ID},
            $address->{AddressModel::FIELD_ID},
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
    public function setDefaultAddress(CustomerModel $customer, AddressModel $address, $type)
    {
        Permissions::check($customer, Permission::edit());

        $this->setDefaultAddressImpl($customer->{CustomerModel::FIELD_ID}, $address->{AddressModel::FIELD_ID}, $type);

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
                ->where(CustomerAddressModel::FIELD_ID, '=', $customerAddress->{CustomerAddressModel::FIELD_ID})
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
     * @return CustomerAddress
     */
    private function getCustomerAndAddressLink($customerId, $addressId, $type)
    {
        /** @var CustomerAddressModel $customerAddress */
        /** @noinspection PhpUndefinedMethodInspection */
        $customerAddress = $this->customerAddressModel->where([
            CustomerAddress::FIELD_ID_CUSTOMER => $customerId,
            CustomerAddress::FIELD_ID_ADDRESS  => $addressId,
            CustomerAddress::FIELD_TYPE        => $type,
        ])->firstOrFail();

        return $customerAddress;
    }
}
