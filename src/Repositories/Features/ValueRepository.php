<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class ValueRepository extends CodeBasedResourceRepository implements ValueRepositoryInterface
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
    public function instance(Feature $feature, array $attributes)
    {
        /** @var FeatureValue $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $feature, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        FeatureValue $resource,
        Feature $feature = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            FeatureValue::FIELD_ID_FEATURE => $feature,
        ], $attributes);
    }
}
