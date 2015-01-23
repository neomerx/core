<?php namespace Neomerx\Core\Repositories\Warehouses;

use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class WarehouseRepository extends CodeBasedResourceRepository implements WarehouseRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Warehouse::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes, Address $address = null, Store $store = null)
    {
        /** @var Warehouse $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes, $address, $store);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Warehouse $resource, array $attributes = null, Address $address = null, Store $store = null)
    {
        $this->fillModel($resource, [
            Warehouse::FIELD_ID_ADDRESS => $address,
            Warehouse::FIELD_ID_STORE   => $store,
        ], $attributes);
    }
}
