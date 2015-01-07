<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Inventory as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Inventory\InventoriesInterface as Api;

class InventoryConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Model $resource
     *
     * @return array
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $result = $resource->attributesToArray();
        $result[Api::PARAM_WAREHOUSE_CODE] = $resource->warehouse->code;

        return $result;
    }
}
