<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;

interface RegionRepositoryInterface extends SearchableInterface
{
    /**
     * @param Country $country
     * @param array   $attributes
     *
     * @return Region
     */
    public function instance(Country $country, array $attributes);

    /**
     *
     * @param Region       $resource
     * @param Country|null $country
     * @param array|null   $attributes
     *
     * @return void
     */
    public function fill(Region $resource, Country $country = null, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Region
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
