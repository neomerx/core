<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\CustomerType as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class CustomerTypeConverterGeneric implements ConverterInterface
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

        return $resource->attributesToArray();
    }
}
