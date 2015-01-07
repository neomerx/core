<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\OrderStatus as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Orders\OrderStatusesInterface as Api;

class OrderStatusConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @inheritdoc
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $statuses = [];
        foreach ($resource->{Model::FIELD_AVAILABLE_STATUSES} as $status) {
            /** @var Model $status */
            $statuses[] = $status->attributesToArray();
        }

        $result = $resource->attributesToArray();
        $result[Api::PARAM_AVAILABLE_STATUSES]  = $statuses;

        return $result;
    }
}
