<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Features\CharacteristicPropertiesRepositoryInterface as PropertiesRepositoryInterface;

class CharacteristicPropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CharacteristicProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Characteristic $resource, Language $language, array $attributes)
    {
        /** @var CharacteristicProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CharacteristicProperties $properties,
        Characteristic $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            CharacteristicProperties::FIELD_ID_CHARACTERISTIC => $resource,
            CharacteristicProperties::FIELD_ID_LANGUAGE       => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
