<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\RepositoryInterface;
use \Neomerx\Core\Repositories\SearchableInterface;

interface RegionRepositoryInterface extends RepositoryInterface, SearchableInterface
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
     * @param array  $relations
     * @param array  $columns
     *
     * @return Region
     */
    public function read($code, array $relations = [], array $columns = ['*']);
}
