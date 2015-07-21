<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Models\FeatureValueProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Features\ValuePropertiesRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class ValuePropertiesRepository extends IndexBasedResourceRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(FeatureValueProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(FeatureValue $resource, Language $language, array $attributes)
    {
        /** @var FeatureValueProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        FeatureValueProperties $properties,
        FeatureValue $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            FeatureValueProperties::FIELD_ID_FEATURE_VALUE => $resource,
            FeatureValueProperties::FIELD_ID_LANGUAGE      => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
