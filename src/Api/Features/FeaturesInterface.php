<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;
use \Illuminate\Database\Eloquent\Collection;

interface FeaturesInterface extends CrudInterface
{
    /**
     * Create characteristic.
     *
     * @param array $input
     *
     * @return Characteristic
     */
    public function create(array $input);

    /**
     * Read characteristic by identifier.
     *
     * @param string $code
     *
     * @return Characteristic
     */
    public function read($code);

    /**
     * Search features.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);

    /**
     * Get all values for characteristic.
     *
     * @param Characteristic $characteristic
     *
     * @return Collection
     */
    public function allValues(Characteristic $characteristic);

    /**
     * Add values to characteristic.
     *
     * @param Characteristic $characteristic
     * @param array          $input
     *
     * @return void
     */
    public function addValues(Characteristic $characteristic, array $input);

    /**
     * Read characteristic value by its code.
     *
     * @param string $code
     *
     * @return CharacteristicValue
     */
    public function readValue($code);

    /**
     * Update characteristic value with code $code.
     *
     * @param string $code
     * @param array  $input
     *
     * @return void
     */
    public function updateValue($code, array $input);

    /**
     * Delete characteristic value with code $code.
     *
     * @param string $code
     *
     * @return void
     */
    public function deleteValue($code);
}
