<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Characteristic $characteristic
     * @param array          $attributes
     *
     * @return CharacteristicValue
     */
    public function instance(Characteristic $characteristic, array $attributes);

    /**
     * @param CharacteristicValue $resource
     * @param Characteristic|null $characteristic
     * @param array|null          $attributes
     *
     * @return void
     */
    public function fill(
        CharacteristicValue $resource,
        Characteristic $characteristic = null,
        array $attributes = null
    );

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CharacteristicValue
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
