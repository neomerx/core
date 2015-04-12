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
    public function instance(Address $address, array $attributes);

    /**
     * @param Manufacturer $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     * @return void
     */
    public function fill(Manufacturer $resource, Address $address = null, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Manufacturer
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
