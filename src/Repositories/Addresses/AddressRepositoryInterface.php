<?php namespace Neomerx\Core\Repositories\Addresses;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface AddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Region $region
     * @param array  $attributes
     *
     * @return Address
     */
    public function instance(Region $region, array $attributes);

    /**
     * @param Address     $resource
     * @param Region|null $region
     * @param array|null  $attributes
     *
     * @return void
     */
    public function fill(Address $resource, Region $region = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Address
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
