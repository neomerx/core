<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Database\Eloquent\Collection;

trait LanguagePropertiesTrait
{
    /**
     * @param Collection $properties
     * @param string     $languageField
     * @param string     $languageFilter
     *
     * @return array
     */
    private function regroupLanguageProperties(Collection $properties, $languageField, $languageFilter = null)
    {
        $convertedProps = [];
        foreach ($properties as $property) {
            /** @var BaseModel $property */
            $propertyIso = $property->{$languageField}->{Language::FIELD_ISO_CODE};
            if (!isset($languageFilter) or /* filter set and filter equals iso */ $propertyIso === $languageFilter) {
                $convertedProps[$propertyIso] = $property->attributesToArray();
            }
        }
        return $convertedProps;
    }
}
