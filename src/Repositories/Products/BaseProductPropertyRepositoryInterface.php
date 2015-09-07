<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\BaseProductProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface BaseProductPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return BaseProductProperty
     */
    public function createWithObjects(BaseProduct $resource, Language $language, array $attributes);

    /**
     * @param int   $baseId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return BaseProductProperty
     */
    public function create($baseId, $languageId, array $attributes);

    /**
     * @param BaseProductProperty $properties
     * @param BaseProduct|null      $resource
     * @param Language|null         $language
     * @param array|null            $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        BaseProductProperty $properties,
        BaseProduct $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param BaseProductProperty $properties
     * @param int|null              $baseId
     * @param int|null              $languageId
     * @param array|null            $attributes
     *
     * @return void
     */
    public function update(
        BaseProductProperty $properties,
        $baseId = null,
        $languageId = null,
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
