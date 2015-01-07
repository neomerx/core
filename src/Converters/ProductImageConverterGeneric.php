<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ProductImage as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class ProductImageConverterGeneric extends ImageConverterGeneric
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

        $result = array_merge($resource->attributesToArray(), parent::convert($resource->image));

        return $result;
    }
}
