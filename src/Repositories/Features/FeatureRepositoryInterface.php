<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface FeatureRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array            $attributes
     * @param Measurement|null $measurement
     *
     * @return Feature
     */
    public function instance(array $attributes, Measurement $measurement = null);

    /**
     * @param Feature          $resource
     * @param array|null       $attributes
     * @param Measurement|null $measurement
     *
     * @return void
     */
    public function fill(Feature $resource, array $attributes = null, Measurement $measurement = null);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Feature
     */
    public function read($index, array $scopes = [], array $columns = ['*']);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Feature
     */
    public function readByCode($code, array $scopes = [], array $columns = ['*']);
}
