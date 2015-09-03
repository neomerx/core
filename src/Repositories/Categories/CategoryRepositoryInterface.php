<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Category
     */
    public function create(array $attributes);

    /**
     * @param Category $resource
     * @param array    $attributes
     *
     * @return void
     */
    public function update(Category $resource, array $attributes);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Category
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
