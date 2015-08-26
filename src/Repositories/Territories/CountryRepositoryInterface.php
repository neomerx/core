<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CountryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Country
     */
    public function instance(array $attributes);

    /**
     * @param Country    $country
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Country $country, array $attributes = null);

    /**
     * @param string $index
     * @param array  $relations
     * @param array  $columns
     *
     * @return Country
     */
    public function read($index, array $relations = [], array $columns = ['*']);

    /**
     * @param string $code
     * @param array  $relations
     * @param array  $columns
     *
     * @return Country
     */
    public function readByCode($code, array $relations = [], array $columns = ['*']);

    /**
     * @param Country $resource
     *
     * @return Collection
     */
    public function regions(Country $resource);
}
