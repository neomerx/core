<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\RepositoryInterface;
use \Neomerx\Core\Models\CharacteristicValueProperties;

interface ValuePropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CharacteristicValue $resource
     * @param Language            $language
     * @param array               $attributes
     *
     * @return CharacteristicValueProperties
     */
    public function instance(CharacteristicValue $resource, Language $language, array $attributes);

    /**
     * @param CharacteristicValueProperties $properties
     * @param CharacteristicValue|null      $resource
     * @param Language|null                 $language
     * @param array|null                    $attributes
     *
     * @return void
     */
    public function fill(
        CharacteristicValueProperties $properties,
        CharacteristicValue $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return CharacteristicValue
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
