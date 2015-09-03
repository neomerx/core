<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Models\FeatureValueProperties;
use \Neomerx\Core\Repositories\Features\ValuePropertiesRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class ValuePropertiesRepository extends BaseRepository implements PropertiesRepositoryInterface
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
    public function createWithObjects(FeatureValue $resource, Language $language, array $attributes)
    {
        return $this->create($this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($resourceId, $languageId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($resourceId, $languageId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        FeatureValueProperties $properties,
        FeatureValue $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        FeatureValueProperties $properties,
        $resourceId = null,
        $languageId = null,
        array $attributes = null
    ) {
        $this->updateWith($properties, $attributes, $this->getRelationships($resourceId, $languageId));
    }

    /**
     * @param int $resourceId
     * @param int $languageId
     *
     * @return array
     */
    protected function getRelationships($resourceId, $languageId)
    {
        return $this->filterNulls([
            FeatureValueProperties::FIELD_ID_FEATURE_VALUE => $resourceId,
            FeatureValueProperties::FIELD_ID_LANGUAGE      => $languageId,
        ]);
    }
}
