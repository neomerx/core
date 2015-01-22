<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface MeasurementRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Measurement
     */
    public function instance(array $attributes);

    /**
     * @param Measurement $resource
     * @param array       $attributes
     *
     * @return void
     */
    public function fill(Measurement $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Measurement
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
