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
    public function createWithObjects(Country $country, array $attributes);

    /**
     * @param int   $countryId
     * @param array $attributes
     *
     * @return Region
     */
    public function create($countryId, array $attributes);

    /**
     *
     * @param Region       $resource
     * @param Country|null $country
     * @param array|null   $attributes
     *
     * @return void
     */
    public function updateWithObjects(Region $resource, Country $country = null, array $attributes = null);

    /**
     *
     * @param Region     $resource
     * @param int|null   $countryId
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Region $resource, $countryId = null, array $attributes = null);

    /**
     * @param int    $index
     * @param array  $relations
     * @param array  $columns
     *
     * @return Region
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
