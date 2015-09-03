<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface ProductPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product  $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return ProductProperties
     */
    public function createWithObjects(Product $resource, Language $language, array $attributes);

    /**
     * @param int   $product
     * @param int   $languageId
     * @param array $attributes
     *
     * @return ProductProperties
     */
    public function create($product, $languageId, array $attributes);

    /**
     * @param ProductProperties $properties
     * @param Product|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        ProductProperties $properties,
        Product $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param ProductProperties $properties
     * @param int|null          $product
     * @param int|null          $languageId
     * @param array|null        $attributes
     *
     * @return void
     */
    public function update(
        ProductProperties $properties,
        $product = null,
        $languageId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Product
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
