<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Inventory\InventoriesInterface as Api;

class InventoryConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Inventory $inventory
     *
     * @return array
     */
    public function convert($inventory = null)
    {
        if ($inventory === null) {
            return null;
        }

        ($inventory instanceof Inventory) ?: S\throwEx(new InvalidArgumentException('inventory'));

        $result = $inventory->attributesToArray();
        $result[Api::PARAM_WAREHOUSE_CODE] = $inventory->warehouse->code;

        return $result;
    }
}
