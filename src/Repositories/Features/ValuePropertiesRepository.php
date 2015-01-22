<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Models\CharacteristicValueProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Features\ValuePropertiesRepositoryInterface as PropertiesRepositoryInterface;

class ValuePropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CharacteristicValueProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(CharacteristicValue $resource, Language $language, array $attributes)
    {
        /** @var CharacteristicValueProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CharacteristicValueProperties $properties,
        CharacteristicValue $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            CharacteristicValueProperties::FIELD_ID_CHARACTERISTIC_VALUE => $resource,
            CharacteristicValueProperties::FIELD_ID_LANGUAGE             => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
