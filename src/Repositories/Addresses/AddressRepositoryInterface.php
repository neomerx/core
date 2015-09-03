<?php namespace Neomerx\Core\Repositories\Addresses;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface AddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array         $attributes
     * @param Nullable|null $region Region
     *
     * @return Address
     */
    public function createWithObjects(array $attributes, Nullable $region = null);

    /**
     * @param array         $attributes
     * @param Nullable|null $regionId
     *
     * @return Address
     */
    public function create(array $attributes, Nullable $regionId = null);

    /**
     * @param Address       $resource
     * @param array|null    $attributes
     * @param Nullable|null $region Region
     *
     * @return void
     */
    public function updateWithObjects(Address $resource, array $attributes = [], Nullable $region = null);

    /**
     * @param Address       $resource
     * @param array|null    $attributes
     * @param Nullable|null $regionId
     *
     * @return void
     */
    public function update(Address $resource, array $attributes = [], Nullable $regionId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Address
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
