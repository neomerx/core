<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SupplierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array   $attributes
     * @param Address $address
     *
     * @return Supplier
     */
    public function createWithObjects(Address $address, array $attributes);

    /**
     * @param array $attributes
     * @param int   $addressId
     *
     * @return Supplier
     */
    public function create($addressId, array $attributes);

    /**
     * @param Supplier     $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     * @return void
     */
    public function updateWithObjects(Supplier $resource, Address $address = null, array $attributes = []);

    /**
     * @param Supplier   $resource
     * @param int|null   $addressId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Supplier $resource, $addressId = null, array $attributes = []);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Supplier
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
