<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class ProductTaxTypeConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param ProductTaxType $productTaxType
     *
     * @return array
     */
    public function convert($productTaxType = null)
    {
        if ($productTaxType === null) {
            return null;
        }

        ($productTaxType instanceof ProductTaxType) ?: S\throwEx(new InvalidArgumentException('productTaxType'));

        return $productTaxType->attributesToArray();
    }
}
