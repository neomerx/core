<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class CharacteristicRepository extends CodeBasedResourceRepository implements CharacteristicRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Characteristic::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes, Measurement $measurement = null)
    {
        /** @var Characteristic $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes, $measurement);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Characteristic $resource, array $attributes = null, Measurement $measurement = null)
    {
        $this->fillModel($resource, [
            Characteristic::FIELD_ID_MEASUREMENT => $measurement,
        ], $attributes);
    }
}
