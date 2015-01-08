<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Orders\OrderStatusesInterface as Api;

class OrderStatusConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param OrderStatus $orderStatus
     *
     * @return array
     */
    public function convert($orderStatus = null)
    {
        if ($orderStatus === null) {
            return null;
        }

        ($orderStatus instanceof OrderStatus) ?: S\throwEx(new InvalidArgumentException('orderStatus'));

        $statuses = [];
        foreach ($orderStatus->{OrderStatus::FIELD_AVAILABLE_STATUSES} as $availableStatus) {
            /** @var \Neomerx\Core\Models\OrderStatus $availableStatus */
            $statuses[] = $availableStatus->attributesToArray();
        }

        $result = $orderStatus->attributesToArray();
        $result[Api::PARAM_AVAILABLE_STATUSES]  = $statuses;

        return $result;
    }
}
