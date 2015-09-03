<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class FeatureRepository extends BaseRepository implements FeatureRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Feature::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(array $attributes, Nullable $measurement = null)
    {
        return $this->create($attributes, $this->idOfNullable($measurement, Measurement::class));
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes, Nullable $measurementId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($measurementId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(Feature $resource, array $attributes = null, Nullable $measurement = null)
    {
        $this->update($resource, $attributes, $this->idOfNullable($measurement, Measurement::class));
    }

    /**
     * @inheritdoc
     */
    public function update(Feature $resource, array $attributes = null, Nullable $measurementId = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($measurementId));
    }

    /**
     * @param Nullable|null $measurementId
     *
     * @return array
     */
    protected function getRelationships(Nullable $measurementId = null)
    {
        return $this->filterNulls([], [
            Feature::FIELD_ID_MEASUREMENT => $measurementId,
        ]);
    }
}
