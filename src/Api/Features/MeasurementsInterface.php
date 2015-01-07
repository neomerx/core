<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\Measurement;
use \Illuminate\Database\Eloquent\Collection;

interface MeasurementsInterface extends CrudInterface
{
    const PARAM_PROPERTIES = Measurement::FIELD_PROPERTIES;

    /**
     * Create measurement.
     *
     * @param array $input
     *
     * @return Measurement
     */
    public function create(array $input);

    /**
     * Read measurement by identifier.
     *
     * @param string $code
     *
     * @return Measurement
     */
    public function read($code);

    /**
     * Get all measurement units in the system.
     *
     * @return Collection
     */
    public function all();
}
