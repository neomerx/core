<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @package Neomerx\Core
 */
interface RepositoryInterface
{
    /**
     * Read resource.
     *
     * @param string|int $key
     * @param array      $relations
     * @param array      $columns
     *
     * @return BaseModel
     */
    public function read($key, array $relations = [], array $columns = ['*']);

    /**
     * Search resources.
     * If both $parameters and $rules are not specified then all resources will be returned.
     *
     * @param array      $relations
     * @param array|null $parameters
     * @param array|null $rules
     * @param array      $columns
     *
     * @return Collection
     */
    public function search(
        array $relations = [],
        array $parameters = null,
        array $rules = null,
        array $columns = ['*']
    );
}
