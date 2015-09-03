<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface MeasurementRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Measurement
     */
    public function create(array $attributes);

    /**
     * @param Measurement $resource
     * @param array       $attributes
     *
     * @return void
     */
    public function update(Measurement $resource, array $attributes);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Measurement
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
