<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ManufacturerProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Manufacturers\ManufacturerPropertiesRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class ManufacturerPropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(ManufacturerProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Manufacturer $resource, Language $language, array $attributes)
    {
        /** @var ManufacturerProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        ManufacturerProperties $properties,
        Manufacturer $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            ManufacturerProperties::FIELD_ID_MANUFACTURER => $resource,
            ManufacturerProperties::FIELD_ID_LANGUAGE     => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
