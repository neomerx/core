<?php namespace Neomerx\Core\Repositories\Warehouses;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Warehouse::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(array $attributes, Nullable $address = null, Nullable $store = null)
    {
        return $this->create(
            $attributes,
            $this->idOfNullable($address, Address::class),
            $this->idOfNullable($store, Store::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes, Nullable $addressId = null, Nullable $storeId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($addressId, $storeId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        Warehouse $resource,
        array $attributes = null,
        Nullable $address = null,
        Nullable $store = null
    ) {
        $this->update(
            $resource,
            $attributes,
            $this->idOfNullable($address, Address::class),
            $this->idOfNullable($store, Store::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function update(
        Warehouse $resource,
        array $attributes = [],
        Nullable $addressId = null,
        Nullable $storeId = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($addressId, $storeId));
    }

    /**
     * @param Nullable|null $addressId
     * @param Nullable|null $storeId
     *
     * @return array
     */
    protected function getRelationships(Nullable $addressId = null, Nullable $storeId = null)
    {
        return $this->filterNulls([], [
            Warehouse::FIELD_ID_ADDRESS => $addressId,
            Warehouse::FIELD_ID_STORE   => $storeId,
        ]);
    }
}
