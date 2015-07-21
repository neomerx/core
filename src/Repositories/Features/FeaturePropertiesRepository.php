<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Features\FeaturePropertiesRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class FeaturePropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(FeatureProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Feature $resource, Language $language, array $attributes)
    {
        /** @var FeatureProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        FeatureProperties $properties,
        Feature $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            FeatureProperties::FIELD_ID_FEATURE  => $resource,
            FeatureProperties::FIELD_ID_LANGUAGE => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
