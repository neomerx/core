<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\CategoryProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Categories\CategoriesInterface as Api;

class CategoryConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Category $category
     *
     * @return array
     */
    public function convert($category = null)
    {
        if ($category === null) {
            return null;
        }

        ($category instanceof Category) ?: S\throwEx(new InvalidArgumentException('category'));

        $result = $category->attributesToArray();

        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $category->properties,
            CategoryProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
