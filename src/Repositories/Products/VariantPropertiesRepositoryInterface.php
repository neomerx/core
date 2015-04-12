<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\VariantProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface VariantPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Variant  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return VariantProperties
     */
    public function instance(Variant $resource, Language $language, array $attributes);

    /**
     * @param VariantProperties $properties
     * @param Variant|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function fill(
        VariantProperties $properties,
        Variant $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Variant
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
