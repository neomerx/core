<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\MeasurementProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Features\MeasurementPropertiesRepositoryInterface as PropertiesRepositoryInterface;

class MeasurementPropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(MeasurementProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Measurement $resource, Language $language, array $attributes)
    {
        /** @var MeasurementProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        MeasurementProperties $properties,
        Measurement $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            MeasurementProperties::FIELD_ID_MEASUREMENT => $resource,
            MeasurementProperties::FIELD_ID_LANGUAGE    => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
