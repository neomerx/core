<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Category::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Category $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
