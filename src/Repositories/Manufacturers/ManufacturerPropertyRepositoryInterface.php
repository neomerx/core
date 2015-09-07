<?php namespace Neomerx\Core\Repositories\Manufacturers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ManufacturerProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ManufacturerPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Manufacturer $resource
     * @param Language     $language
     * @param array        $attributes
     *
     * @return ManufacturerProperty
     */
    public function createWithObjects(Manufacturer $resource, Language $language, array $attributes);

    /**
     * @param int   $manufacturerId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return ManufacturerProperty
     */
    public function create($manufacturerId, $languageId, array $attributes);

    /**
     * @param ManufacturerProperty $properties
     * @param Manufacturer|null      $resource
     * @param Language|null          $language
     * @param array|null             $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ManufacturerProperty $properties,
        Manufacturer $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param ManufacturerProperty $properties
     * @param int|null               $manufacturerId
     * @param int|null               $languageId
     * @param array|null             $attributes
     *
     * @return void
     */
    public function update(
        ManufacturerProperty $properties,
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
