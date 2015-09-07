<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Models\MeasurementProperty;
use \Neomerx\Core\Repositories\Features\MeasurementPropertyRepositoryInterface as PropertiesRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class MeasurementPropertyRepository extends BaseRepository implements PropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(MeasurementProperty::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Measurement $resource, Language $language, array $attributes)
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
        MeasurementProperty $properties,
        Measurement $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update($properties, $this->idOf($resource), $this->idOf($language), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        MeasurementProperty $properties,
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
            MeasurementProperty::FIELD_ID_MEASUREMENT => $resourceId,
            MeasurementProperty::FIELD_ID_LANGUAGE    => $languageId,
        ]);
    }
}
