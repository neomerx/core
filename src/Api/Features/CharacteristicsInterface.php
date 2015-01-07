<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\Characteristic;
use \Illuminate\Database\Eloquent\Collection;

interface CharacteristicsInterface extends CrudInterface
{
    const PARAM_PROPERTIES       = Characteristic::FIELD_PROPERTIES;
    const PARAM_MEASUREMENT_CODE = 'measurement_code';

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
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
