<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ValueRepository extends CodeBasedResourceRepository implements ValueRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CharacteristicValue::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Characteristic $characteristic, array $attributes)
    {
        /** @var CharacteristicValue $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $characteristic, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CharacteristicValue $resource,
        Characteristic $characteristic = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            CharacteristicValue::FIELD_ID_CHARACTERISTIC => $characteristic,
        ], $attributes);
    }
}
