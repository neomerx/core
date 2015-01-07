<?php namespace Neomerx\Core\Api\Warehouses;

use \Neomerx\Core\Models\Warehouse;

interface WarehousesInterface
{
    /**
     * Get default warehouse.
     *
     * @return Warehouse
     */
    public function getDefault();
}
