<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class MeasurementRepository extends BaseRepository implements MeasurementRepositoryInterface
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
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Measurement $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
