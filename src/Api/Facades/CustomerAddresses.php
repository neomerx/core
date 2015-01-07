<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Customer;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Customers\CustomerAddressesInterface;

/**
 * @see CustomerAddressesInterface
 *
 * @method static array   getAddresses(Customer $customer)
 * @method static Address getAddress(Customer $customer, int $addressId)
 * @method static Address createAddress(Customer $customer, array $input)
 * @method static void    deleteAddress(Customer $customer, Address $address, string $type)
 * @method static void    setDefaultAddress(Customer $customer, Address $address, string $type)
 */
final class CustomerAddresses extends Facade
{
    const INTERFACE_BIND_NAME = CustomerAddressesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
