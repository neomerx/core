<?php namespace Neomerx\Core\Api\Cart;

use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\Variant;

interface CartInterface
{
    const PARAM_WEIGHT            = 'weight';
    const PARAM_QUANTITY          = OrderDetails::FIELD_QUANTITY;
    const PARAM_PRICE_WO_TAX      = OrderDetails::FIELD_PRICE_WO_TAX;
    const PARAM_MAX_DIMENSION     = 'max_dimension';
    const PARAM_ITEMS             = 'items';
    const PARAM_ITEM_SKU          = Variant::FIELD_SKU;
    const PARAM_ITEM_PRICE_WO_TAX = OrderDetails::FIELD_PRICE_WO_TAX;
    const PARAM_ITEM_QUANTITY     = OrderDetails::FIELD_QUANTITY;
}
