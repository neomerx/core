<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @package Neomerx\Core
 */
interface RepositoryInterface
{
    /**
     * Read resources.
     *
     * @param array $relations
     * @param array $columns
     *
     * @return Collection
     */
    public function index(array $relations = [], array $columns = ['*']);

    /**
     * Read resource.
     *
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return BaseModel
     */
    public function read($index, array $relations = [], array $columns = ['*']);

    /**
     * Delete resource.
     *
     * @param int $index
     *
     * @return bool
     */
    public function delete($index);
}
