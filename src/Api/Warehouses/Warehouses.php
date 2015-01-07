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
        return $this->warehouseModel->selectByCode(Warehouse::DEFAULT_CODE)->firstOrFail();
    }
}
