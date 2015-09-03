<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CategoryProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CategoryPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Category $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CategoryProperties
     */
    public function createWithObjects(Category $resource, Language $language, array $attributes);

    /**
     * @param int   $categoryId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CategoryProperties
     */
    public function create($categoryId, $languageId, array $attributes);

    /**
     * @param CategoryProperties $properties
     * @param Category|null      $resource
     * @param Language|null      $language
     * @param array|null         $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CategoryProperties $properties,
        Category $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CategoryProperties $properties
     * @param int|null           $categoryId
     * @param int|null           $languageId
     * @param array|null         $attributes
     *
     * @return void
     */
    public function update(
        CategoryProperties $properties,
        $categoryId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Category
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
