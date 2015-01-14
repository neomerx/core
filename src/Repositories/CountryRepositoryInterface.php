<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Country;

interface CountryRepositoryInterface extends RepositoryInterface, SearchableInterface
{
    /**
     * @param array|null $attributes
     *
     * @return Country
     */
    public function instance(array $attributes = null);

    /**
     * @param Country    $country
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Country $country, array $attributes = null);

    /**
     * @param string $code
     * @param array  $relations
     * @param array  $columns
     *
     * @return Country
     */
    public function read($code, array $relations = [], array $columns = ['*']);
}
