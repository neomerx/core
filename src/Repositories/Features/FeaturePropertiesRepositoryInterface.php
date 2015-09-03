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
    public function createWithObjects(Feature $resource, Language $language, array $attributes);

    /**
     * @param int   $featureId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return FeatureProperties
     */
    public function create($featureId, $languageId, array $attributes);

    /**
     * @param FeatureProperties $properties
     * @param Feature|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        FeatureProperties $properties,
        Feature $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param FeatureProperties $properties
     * @param int|null          $featureId
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        FeatureProperties $properties,
        $featureId = null,
        $languageId = null,
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
