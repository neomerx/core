<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface SupplierRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array   $attributes
     * @param Address $address
     *
     * @return Supplier
     */
    public function instance(Address $address, array $attributes);

    /**
     * @param Supplier     $resource
     * @param Address|null $address
     * @param array|null   $attributes
     *
     * @return void
     */
    public function fill(Supplier $resource, Address $address = null, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Supplier
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
