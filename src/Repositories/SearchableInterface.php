<?php namespace Neomerx\Core\Repositories;

use \Illuminate\Database\Eloquent\Collection;

interface SearchableInterface
{
    /**
     * Search resources.
     * If both $parameters and $rules are not specified then all resources will be returned.
     *
     * @param array      $scopes
     * @param array|null $parameters
     * @param array|null $rules
     * @param array      $columns
     *
     * @return Collection
     */
    public function search(array $scopes = [], array $parameters = null, array $rules = null, array $columns = ['*']);
}
