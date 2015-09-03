<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ValueRepository extends BaseRepository implements ValueRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(FeatureValue::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Feature $feature, array $attributes)
    {
        return $this->create($this->idOf($feature), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($featureId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($featureId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        FeatureValue $resource,
        Feature $feature = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($feature), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        FeatureValue $resource,
        $featureId = null,
        array $attributes = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($featureId));
    }

    /**
     * @param int $featureId
     *
     * @return array
     */
    private function getRelationships($featureId)
    {
        return $this->filterNulls([
            FeatureValue::FIELD_ID_FEATURE => $featureId,
        ]);
    }
}
