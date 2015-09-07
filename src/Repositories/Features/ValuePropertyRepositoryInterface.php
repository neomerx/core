<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Models\FeatureValueProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ValuePropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param FeatureValue $resource
     * @param Language     $language
     * @param array        $attributes
     *
     * @return FeatureValueProperty
     */
    public function createWithObjects(FeatureValue $resource, Language $language, array $attributes);

    /**
     * @param int   $valueId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return FeatureValueProperty
     */
    public function create($valueId, $languageId, array $attributes);

    /**
     * @param FeatureValueProperty $properties
     * @param FeatureValue|null      $resource
     * @param Language|null          $language
     * @param array|null             $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        FeatureValueProperty $properties,
        FeatureValue $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param FeatureValueProperty $properties
     * @param int|null               $valueId
     * @param int|null               $languageId
     * @param array|null             $attributes
     *
     * @return void
     */
    public function update(
        FeatureValueProperty $properties,
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
