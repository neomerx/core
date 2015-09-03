<?php namespace Neomerx\Core\Repositories\Warehouses;

use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface WarehouseRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array         $attributes
     * @param Nullable|null $address Address
     * @param Nullable|null $store   Store
     *
     * @return Warehouse
     */
    public function createWithObjects(array $attributes, Nullable $address = null, Nullable $store = null);

    /**
     * @param array         $attributes
     * @param Nullable|null $addressId
     * @param Nullable|null $storeId
     *
     * @return Warehouse
     */
    public function create(array $attributes, Nullable $addressId = null, Nullable $storeId = null);

    /**
     * @param Warehouse     $resource
     * @param array|null    $attributes
     * @param Nullable|null $address Address
     * @param Nullable|null $store   Store
     *
     * @return void
     */
    public function updateWithObjects(
        Warehouse $resource,
        array $attributes = null,
        Nullable $address = null,
        Nullable $store = null
    );

    /**
     * @param Warehouse     $resource
     * @param array|null    $attributes
     * @param Nullable|null $addressId
     * @param Nullable|null $storeId
     *
     * @return void
     */
    public function update(
        Warehouse $resource,
        array $attributes = [],
        Nullable $addressId = null,
        Nullable $storeId = null
    );

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Warehouse
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
