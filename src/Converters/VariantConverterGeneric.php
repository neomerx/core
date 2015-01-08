<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class VariantConverterGeneric extends ProductConverterGeneric
{
    const BIND_NAME = __CLASS__;

    /**
     * @param Variant $variant
     *
     * @return array
     */
    public function convert($variant = null)
    {
        if ($variant === null) {
            return null;
        }

        ($variant instanceof Variant) ?: S\throwEx(new InvalidArgumentException('variant'));

        return array_merge(parent::convert($variant->product), $variant->attributesToArray());
    }
}
