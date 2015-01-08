<?php namespace Neomerx\Core\Api\Warehouses;

use \Neomerx\Core\Models\Warehouse;

class Warehouses implements WarehousesInterface
{
    const EVENT_PREFIX = 'Api.Warehouse.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Warehouse
     */
    private $warehouseModel;

    public function __construct(Warehouse $warehouseModel)
    {
        $this->warehouseModel = $warehouseModel;
    }

    /**
     * @return Warehouse
     */
    public function getDefault()
    {
        /** @var \Neomerx\Core\Models\Warehouse $warehouse */
        $warehouse = $this->warehouseModel->selectByCode(Warehouse::DEFAULT_CODE)->firstOrFail();
        return $warehouse;
    }
}
