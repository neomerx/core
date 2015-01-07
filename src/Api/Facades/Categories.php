<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Category;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Categories\CategoriesInterface;

/**
 * @see CategoriesInterface
 *
 * @method static void       create(array $input)
 * @method static Category   read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection readDescendants(Category $parent, array $parameters = [])
 * @method static void       moveUp(Category $category)
 * @method static void       moveDown(Category $category)
 * @method static void       attach(Category $category, Category $newParent)
 * @method static array      showProducts(Category $category)
 * @method static void       updatePositions(Category $category, array $productPositions) */
class Categories extends Facade
{
    const INTERFACE_BIND_NAME = CategoriesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
