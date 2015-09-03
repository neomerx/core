<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Feature $feature
     * @param array   $attributes
     *
     * @return FeatureValue
     */
    public function createWithObjects(Feature $feature, array $attributes);

    /**
     * @param int   $featureId
     * @param array $attributes
     *
     * @return FeatureValue
     */
    public function create($featureId, array $attributes);

    /**
     * @param FeatureValue $resource
     * @param Feature|null $feature
     * @param array|null   $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        FeatureValue $resource,
        Feature $feature = null,
        array $attributes = null
    );

    /**
     * @param FeatureValue $resource
     * @param int|null     $featureId
     * @param array|null   $attributes
     *
     * @return void
     */
    public function update(
        FeatureValue $resource,
        $featureId = null,
        array $attributes = null
    );

    /**
     * @param int   $index
     * @param array $scopes
     * @param array $columns
     *
     * @return FeatureValue
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
