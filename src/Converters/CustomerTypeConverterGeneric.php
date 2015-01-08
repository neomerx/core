<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class CustomerTypeConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param CustomerType $customerType
     *
     * @return array
     */
    public function convert($customerType = null)
    {
        if ($customerType === null) {
            return null;
        }

        ($customerType instanceof CustomerType) ?: S\throwEx(new InvalidArgumentException('customerType'));

        return $customerType->attributesToArray();
    }
}
