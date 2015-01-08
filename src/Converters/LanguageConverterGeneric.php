<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class LanguageConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Language $language
     *
     * @return array
     */
    public function convert($language = null)
    {
        if ($language === null) {
            return null;
        }

        ($language instanceof Language) ?: S\throwEx(new InvalidArgumentException('language'));

        $result = $language->attributesToArray();

        return $result;
    }
}
