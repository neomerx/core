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
    public function instance(Feature $feature, array $attributes);

    /**
     * @param FeatureValue $resource
     * @param Feature|null $feature
     * @param array|null   $attributes
     *
     * @return void
     */
    public function fill(
        FeatureValue $resource,
        Feature $feature = null,
        array $attributes = null
    );

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return FeatureValue
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
