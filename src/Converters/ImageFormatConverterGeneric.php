<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class ImageFormatConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param ImageFormat $imageFormat
     *
     * @return array
     */
    public function convert($imageFormat = null)
    {
        if ($imageFormat === null) {
            return null;
        }

        ($imageFormat instanceof ImageFormat) ?: S\throwEx(new InvalidArgumentException('imageFormat'));

        $result = $imageFormat->attributesToArray();

        return $result;
    }
}
