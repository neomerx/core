<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Address;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Addresses\AddressesInterface;

/**
 * @see AddressesInterface
 *
 * @method static Address create(array $input)
 * @method static Address read(string $code)
 * @method static void    update(string $code, array $input)
 * @method static void    delete(string $code)
 * @method static void    updateModel(Address $address, array $input)
 * @method static void    deleteModel(Address $address)
 */
class Addresses extends Facade
{
    const INTERFACE_BIND_NAME = AddressesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
