<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CategoryProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CategoryPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Category $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CategoryProperty
     */
    public function createWithObjects(Category $resource, Language $language, array $attributes);

    /**
     * @param int   $categoryId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return CategoryProperty
     */
    public function create($categoryId, $languageId, array $attributes);

    /**
     * @param CategoryProperty $properties
     * @param Category|null      $resource
     * @param Language|null      $language
     * @param array|null         $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        CategoryProperty $properties,
        Category $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param CategoryProperty $properties
     * @param int|null           $categoryId
     * @param int|null           $languageId
     * @param array|null         $attributes
     *
     * @return void
     */
    public function update(
        CategoryProperty $properties,
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
