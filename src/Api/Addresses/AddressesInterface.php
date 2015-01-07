<?php namespace Neomerx\Core\Api\Addresses;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Api\CrudInterface;

interface AddressesInterface extends CrudInterface
{
    const PARAM_ADDRESS1    = Address::FIELD_ADDRESS1;
    const PARAM_ADDRESS2    = Address::FIELD_ADDRESS2;
    const PARAM_CITY        = Address::FIELD_CITY;
    const PARAM_POSTCODE    = Address::FIELD_POSTCODE;
    const PARAM_REGION_CODE = 'region_code';

    /**
     * Create address.
     *
     * @param array $input
     *
     * @return Address
     */
    public function create(array $input);

    /**
     * Read address by identifier.
     *
     * @param int $addressId
     *
     * @return Address
     */
    public function read($addressId);

    /**
     * Update address.
     *
     * @param Address $address
     * @param array   $input
     *
     * @return void
     */
    public function updateModel(Address $address, array $input);

    /**
     * Delete address.
     *
     * @param Address $address
     *
     * @return void
     */
    public function deleteModel(Address $address);
}
