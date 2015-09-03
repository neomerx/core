<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Country;
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
    public function create(array $attributes);

    /**
     * @param Country    $country
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Country $country, array $attributes = null);

    /**
     * @param int    $index
     * @param array  $relations
     * @param array  $columns
     *
     * @return Country
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
