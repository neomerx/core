<?php namespace Neomerx\Core\Repositories\Stores;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface StoreRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Address $address
     * @param array   $attributes
     *
     * @return Store
     */
    public function createWithObjects(Address $address, array $attributes);

    /**
     * @param int   $addressId
     * @param array $attributes
     *
     * @return Store
     */
    public function create($addressId, array $attributes);

    /**
     * @param Store        $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     * @return void
     */
    public function updateWithObjects(Store $resource, Address $address = null, array $attributes = []);

    /**
     * @param Store      $resource
     * @param int|null   $addressId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Store $resource, $addressId = null, array $attributes = []);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Store
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
