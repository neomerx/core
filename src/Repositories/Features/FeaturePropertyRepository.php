<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureProperty;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Features\FeaturePropertyRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class FeaturePropertyRepository extends BaseRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(FeatureProperty::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Feature $resource, Language $language, array $attributes)
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
        FeatureProperty $properties,
        Feature $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        FeatureProperty $properties,
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
            FeatureProperty::FIELD_ID_FEATURE => $resourceId,
            FeatureProperty::FIELD_ID_LANGUAGE => $languageId,
        ]);
    }
}
