<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class MeasurementRepository extends CodeBasedResourceRepository implements MeasurementRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Measurement::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Measurement $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Measurement $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
