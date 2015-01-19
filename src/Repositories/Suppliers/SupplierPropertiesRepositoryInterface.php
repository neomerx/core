<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\SupplierProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface SupplierPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Supplier $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return SupplierProperties
     */
    public function instance(Supplier $resource, Language $language, array $attributes);

    /**
     * @param SupplierProperties $properties
     * @param Supplier|null      $resource
     * @param Language|null      $language
     * @param array|null         $attributes
     *
     * @return void
     */
    public function fill(
        SupplierProperties $properties,
        Supplier $resource = null,
        Language $language = null,
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
