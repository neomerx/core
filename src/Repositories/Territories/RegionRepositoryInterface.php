<?php namespace Neomerx\Core\Repositories\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface RegionRepositoryInterface extends RepositoryInterface
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
     * @param int    $index
     * @param array  $relations
     * @param array  $columns
     *
     * @return Region
     */
    public function read($index, array $relations = [], array $columns = ['*']);

    /**
     * @param string $code
     * @param array  $relations
     * @param array  $columns
     *
     * @return Region
     */
    public function readByCode($code, array $relations = [], array $columns = ['*']);
}
