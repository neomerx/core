<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface FeatureRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array         $attributes
     * @param Nullable|null $measurement Measurement
     *
     * @return Feature
     */
    public function createWithObjects(array $attributes, Nullable $measurement = null);

    /**
     * @param array         $attributes
     * @param Nullable|null $measurementId
     *
     * @return Feature
     */
    public function create(array $attributes, Nullable $measurementId = null);

    /**
     * @param Feature       $resource
     * @param array|null    $attributes
     * @param Nullable|null $measurement Measurement
     *
     * @return void
     */
    public function updateWithObjects(Feature $resource, array $attributes = null, Nullable $measurement = null);

    /**
     * @param Feature       $resource
     * @param array|null    $attributes
     * @param Nullable|null $measurementId
     *
     * @return void
     */
    public function update(Feature $resource, array $attributes = null, Nullable $measurementId = null);

    /**
     * @param int   $index
     * @param array $scopes
     * @param array $columns
     *
     * @return Feature
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
