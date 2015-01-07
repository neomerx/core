<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\ShippingOrders\ShippingOrdersInterface as Api;

class ShippingOrderConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @inheritdoc
     */
    public function convert($shippingOrder = null)
    {
        if ($shippingOrder === null) {
            return null;
        }

        ($shippingOrder instanceof ShippingOrder) ?: S\throwEx(new InvalidArgumentException('shippingOrder'));

        /** @var ShippingOrder $shippingOrder */

        $result = $shippingOrder->attributesToArray();

        $result[Api::PARAM_STATUS_CODE]  = $shippingOrder->status->code;
        $result[Api::PARAM_CARRIER_CODE] = $shippingOrder->carrier->code;

        return $result;
    }
}
