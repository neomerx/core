<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\SupplierProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SupplierPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Supplier  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return SupplierProperty
     */
    public function createWithObjects(Supplier $resource, Language $language, array $attributes);

    /**
     * @param int   $supplierId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return SupplierProperty
     */
    public function create($supplierId, $languageId, array $attributes);

    /**
     * @param SupplierProperty $properties
     * @param Supplier|null      $resource
     * @param Language|null      $language
     * @param array|null         $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        SupplierProperty $properties,
        Supplier $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param SupplierProperty $properties
     * @param int|null           $supplierId
     * @param int|null           $languageId
     * @param array|null         $attributes
     *
     * @return void
     */
    public function update(
        SupplierProperty $properties,
        $supplierId = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Supplier
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
