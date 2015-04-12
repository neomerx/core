<?php namespace Neomerx\Core\Repositories\Categories;

use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CategoryRepository extends CodeBasedResourceRepository implements CategoryRepositoryInterface
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
    public function instance(array $attributes)
    {
        /** @var Category $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Category $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
