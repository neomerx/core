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
    public function createWithObjects(FeatureValue $resource, Language $language, array $attributes);

    /**
     * @param int   $valueId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return FeatureValueProperties
     */
    public function create($valueId, $languageId, array $attributes);

    /**
     * @param FeatureValueProperties $properties
     * @param FeatureValue|null      $resource
     * @param Language|null          $language
     * @param array|null             $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        FeatureValueProperties $properties,
        FeatureValue $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param FeatureValueProperties $properties
     * @param int|null               $valueId
     * @param int|null               $languageId
     * @param array|null             $attributes
     *
     * @return void
     */
    public function update(
        FeatureValueProperties $properties,
        $valueId = null,
        $languageId = null,
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
