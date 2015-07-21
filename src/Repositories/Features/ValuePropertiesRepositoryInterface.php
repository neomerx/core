<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Models\FeatureValueProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ValuePropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param FeatureValue $resource
     * @param Language     $language
     * @param array        $attributes
     *
     * @return FeatureValueProperties
     */
    public function instance(FeatureValue $resource, Language $language, array $attributes);

    /**
     * @param FeatureValueProperties $properties
     * @param FeatureValue|null      $resource
     * @param Language|null          $language
     * @param array|null             $attributes
     *
     * @return void
     */
    public function fill(
        FeatureValueProperties $properties,
        FeatureValue $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return FeatureValue
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
