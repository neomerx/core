<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Feature;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface FeaturePropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Feature  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return FeatureProperties
     */
    public function instance(Feature $resource, Language $language, array $attributes);

    /**
     * @param FeatureProperties $properties
     * @param Feature|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function fill(
        FeatureProperties $properties,
        Feature $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Feature
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
