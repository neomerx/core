<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\SupplyOrders\SupplyOrders as Api;

class SupplyOrderConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param SupplyOrder $supplyOrder
     *
     * @return array
     */
    public function convert($supplyOrder = null)
    {
        if ($supplyOrder === null) {
            return null;
        }

        ($supplyOrder instanceof SupplyOrder) ?: S\throwEx(new InvalidArgumentException('supplyOrder'));

        $result = $supplyOrder->attributesToArray();

        $result[Api::PARAM_CURRENCY_CODE]  = $supplyOrder->currency->code;
        $result[Api::PARAM_LANGUAGE_CODE]  = $supplyOrder->language->iso_code;
        $result[Api::PARAM_SUPPLIER_CODE]  = $supplyOrder->supplier->code;
        $result[Api::PARAM_WAREHOUSE_CODE] = $supplyOrder->warehouse->code;

        $details = [];
        foreach ($supplyOrder->details as $detailsRow) {
            /** @var \Neomerx\Core\Models\SupplyOrderDetails $detailsRow */
            $details[] = $detailsRow->attributesToArray();
            $details[Api::PARAM_DETAILS_SKU] = $detailsRow->variant->sku;
        }
        $result[Api::PARAM_DETAILS] = $details;

        return $result;
    }
}
