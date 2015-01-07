<?php namespace Neomerx\Core\Api\Traits;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Language;

trait LanguagePropertiesTrait
{
    /**
     * Splits input and changes language codes to IDs. Input
     * [
     *  ... => ...
     *  ... => ...
     *  'properties' => [
     *   language_code1 => [...],
     *   language_code2 => [...],
     *  ]
     * ]
     * will be split into
     * [
     *  ... => ...
     *  ... => ...
     * ]
     * and
     * [
     *  language_id1 => [...],
     *  language_id2 => [...],
     * ]
     *
     * @param Language $language
     * @param array    $input
     *
     * @return array
     */
    private function extractPropertiesInput(Language $language, array $input)
    {
        $properties = S\array_get_value($input, 'properties', []);
        unset($input['properties']);

        $propertiesInput = [];
        foreach ($properties as $isoCode => $languageSet) {
            $languageId = $language->selectByCode($isoCode)->firstOrFail([Language::FIELD_ID])->{Language::FIELD_ID};
            $propertiesInput[$languageId] = $languageSet;
        }
        return [$input, $propertiesInput];
    }
}
