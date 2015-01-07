<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Models\SupplyOrder as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\SupplyOrders\SupplyOrders as Api;

class SupplyOrderConverterGeneric implements ConverterInterface
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

        $result[Api::PARAM_CURRENCY_CODE]  = $resource->currency->code;
        $result[Api::PARAM_LANGUAGE_CODE]  = $resource->language->iso_code;
        $result[Api::PARAM_SUPPLIER_CODE]  = $resource->supplier->code;
        $result[Api::PARAM_WAREHOUSE_CODE] = $resource->warehouse->code;

        $details = [];
        foreach ($resource->details as $detailsRow) {
            /** @var SupplyOrderDetails $detailsRow */
            $details[] = $detailsRow->attributesToArray();
            $details[Api::PARAM_DETAILS_SKU] = $detailsRow->variant->sku;
        }
        $result[Api::PARAM_DETAILS] = $details;

        return $result;
    }
}
