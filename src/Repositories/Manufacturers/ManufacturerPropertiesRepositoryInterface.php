<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ManufacturerProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ManufacturerPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Manufacturer $resource
     * @param Language     $language
     * @param array        $attributes
     *
     * @return ManufacturerProperties
     */
    public function createWithObjects(Manufacturer $resource, Language $language, array $attributes);

    /**
     * @param int   $manufacturerId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return ManufacturerProperties
     */
    public function create($manufacturerId, $languageId, array $attributes);

    /**
     * @param ManufacturerProperties $properties
     * @param Manufacturer|null      $resource
     * @param Language|null          $language
     * @param array|null             $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ManufacturerProperties $properties,
        Manufacturer $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param ManufacturerProperties $properties
     * @param int|null               $manufacturerId
     * @param int|null               $languageId
     * @param array|null             $attributes
     *
     * @return void
     */
    public function update(
        ManufacturerProperties $properties,
        $manufacturerId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Manufacturer
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
