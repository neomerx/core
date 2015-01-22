<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CategoryProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class CategoryPropertiesRepository extends IndexBasedResourceRepository implements CategoryPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CategoryProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Category $resource, Language $language, array $attributes)
    {
        /** @var CategoryProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CategoryProperties $properties,
        Category $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            CategoryProperties::FIELD_ID_CATEGORY => $resource,
            CategoryProperties::FIELD_ID_LANGUAGE => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
