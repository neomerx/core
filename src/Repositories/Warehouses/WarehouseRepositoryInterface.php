<?php namespace Neomerx\Core\Repositories\Warehouses;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface WarehouseRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array        $attributes
     * @param Address|null $address
     * @param Store|null   $store
     *
     * @return Warehouse
     */
    public function instance(array $attributes, Address $address = null, Store $store = null);

    /**
     * @param Warehouse    $resource
     * @param array|null   $attributes
     * @param Address|null $address
     * @param Store|null   $store
     *
     * @return void
     */
    public function fill(Warehouse $resource, array $attributes = null, Address $address = null, Store $store = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Warehouse
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
