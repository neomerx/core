<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CarrierProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CarrierPropertiesRepository extends IndexBasedResourceRepository implements CarrierPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CarrierProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Carrier $resource, Language $language, array $attributes)
    {
        /** @var CarrierProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CarrierProperties $properties,
        Carrier $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [CarrierProperties::FIELD_ID_CARRIER => $resource, CarrierProperties::FIELD_ID_LANGUAGE => $language];
        $this->fillModel($properties, $data, $attributes);
    }
}
