<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\BaseProductProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface BaseProductPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct $resource
     * @param Language    $language
     * @param array       $attributes
     *
     * @return BaseProductProperties
     */
    public function instance(BaseProduct $resource, Language $language, array $attributes);

    /**
     * @param BaseProductProperties $properties
     * @param BaseProduct|null      $resource
     * @param Language|null         $language
     * @param array|null            $attributes
     *
     * @return void
     */
    public function fill(
        BaseProductProperties $properties,
        BaseProduct $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return BaseProduct
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
