<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ManufacturerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array   $attributes
     * @param Address $address
     *
     * @return Manufacturer
     */
    public function createWithObjects(Address $address, array $attributes);

    /**
     * @param array $attributes
     * @param int   $addressId
     *
     * @return Manufacturer
     */
    public function create($addressId, array $attributes);

    /**
     * @param Manufacturer $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     * @return void
     */
    public function updateWithObjects(Manufacturer $resource, Address $address = null, array $attributes = null);

    /**
     * @param Manufacturer $resource
     * @param int|null     $addressId
     * @param array|null   $attributes
     *
     * @return void
     */
    public function update(Manufacturer $resource, $addressId = null, array $attributes = null);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Manufacturer
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
