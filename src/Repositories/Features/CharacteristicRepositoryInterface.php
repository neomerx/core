<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CharacteristicRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array            $attributes
     * @param Measurement|null $measurement
     *
     * @return Characteristic
     */
    public function instance(array $attributes, Measurement $measurement = null);

    /**
     * @param Characteristic   $resource
     * @param array|null       $attributes
     * @param Measurement|null $measurement
     *
     * @return void
     */
    public function fill(Characteristic $resource, array $attributes = null, Measurement $measurement = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Characteristic
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
