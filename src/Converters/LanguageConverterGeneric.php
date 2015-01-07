<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Language as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class LanguageConverterGeneric implements ConverterInterface
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

        return $result;
    }
}
