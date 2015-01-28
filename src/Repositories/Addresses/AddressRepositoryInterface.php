<?php namespace Neomerx\Core\Repositories\Addresses;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface AddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array       $attributes
     * @param Region|null $region
     *
     * @return Address
     */
    public function instance(array $attributes, Region $region = null);

    /**
     * @param Address     $resource
     * @param array|null  $attributes
     * @param Region|null $region
     *
     * @return void
     */
    public function fill(Address $resource, array $attributes = null, Region $region = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Address
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
