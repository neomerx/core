<?php namespace Neomerx\Core\Repositories\Stores;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface StoreRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Address $address
     * @param array   $attributes
     *
     * @return Store
     */
    public function instance(Address $address, array $attributes);

    /**
     * @param Store        $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     */
    public function fill(Store $resource, Address $address = null, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Store
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
