<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int   $ancestorId
     * @param array $attributes
     *
     * @return Category
     */
    public function create($ancestorId, array $attributes);

    /**
     * @param Category $resource
     * @param int|null $ancestorId
     * @param array    $attributes
     *
     * @return void
     */
    public function update(Category $resource, $ancestorId = null, array $attributes = []);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Category
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

    /**
     * Read descendant categories.
     *
     * @param int $index
     *
     * @return Collection
     */
    public function readDescendants($index);

    /**
     * Switch the subtree node with a node on the left if there are any with the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveLeft(Category $category);

    /**
     * Switch the subtree node with a node on the right if there are any with the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveRight(Category $category);
}
