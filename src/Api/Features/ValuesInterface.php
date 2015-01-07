<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;
use \Illuminate\Database\Eloquent\Collection;

interface ValuesInterface extends CrudInterface
{
    const PARAM_PROPERTIES          = CharacteristicValue::FIELD_PROPERTIES;
    const PARAM_CHARACTERISTIC_CODE = 'characteristic_code';

    /**
     * Create characteristic value.
     *
     * @param array $input
     *
     * @return CharacteristicValue
     */
    public function create(array $input);

    /**
     * Read characteristic value by identifier.
     *
     * @param string $code
     *
     * @return CharacteristicValue
     */
    public function read($code);

    /**
     * @param Characteristic $characteristic
     *
     * @return Collection
     */
    public function all(Characteristic $characteristic);
}
