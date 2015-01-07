<?php namespace Neomerx\Core\Api\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface CategoriesInterface extends CrudInterface
{
    const PARAM_PROPERTIES = Category::FIELD_PROPERTIES;

    /**
     * Create category.
     *
     * @param array $input
     *
     * @return Category
     */
    public function create(array $input);

    /**
     * Read category by identifier.
     *
     * @param string $code
     *
     * @return Category
     */
    public function read($code);

    /**
     * Read descendant categories.
     *
     * @param Category $parent
     *
     * @return Collection
     */
    public function readDescendants(Category $parent);

    /**
     * Switch place of the category with upper category of the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveUp(Category $category);

    /**
     * Switch place of the category with lower category of the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveDown(Category $category);

    /**
     * Attach category with code $code to category with code $attachTo.
     *
     * @param Category $category
     * @param Category $newParent
     *
     * @return void
     */
    public function attach(Category $category, Category $newParent);

    /**
     * Read products in category.
     *
     * @param Category $category
     *
     * @return array Array of pairs [$product, $positionInCategory]
     */
    public function showProducts(Category $category);

    /**
     * @param Category $category
     * @param array    $productPositions Product code and position pairs.
     *
     * @return void
     */
    public function updatePositions(Category $category, array $productPositions);
}
