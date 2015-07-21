<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class FeatureRepository extends CodeBasedResourceRepository implements FeatureRepositoryInterface
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
    public function instance(array $attributes, Measurement $measurement = null)
    {
        /** @var Feature $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes, $measurement);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Feature $resource, array $attributes = null, Measurement $measurement = null)
    {
        $this->fillModel($resource, [
            Feature::FIELD_ID_MEASUREMENT => $measurement,
        ], $attributes);
    }
}
