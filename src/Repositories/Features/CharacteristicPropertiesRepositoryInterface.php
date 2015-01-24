<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Models\CharacteristicProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface CharacteristicPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Characteristic $resource
     * @param Language       $language
     * @param array          $attributes
     *
     * @return CharacteristicProperties
     */
    public function instance(Characteristic $resource, Language $language, array $attributes);

    /**
     * @param CharacteristicProperties $properties
     * @param Characteristic|null      $resource
     * @param Language|null            $language
     * @param array|null               $attributes
     *
     * @return void
     */
    public function fill(
        CharacteristicProperties $properties,
        Characteristic $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Characteristic
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
