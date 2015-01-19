<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;

interface VariantRepositoryInterface extends RepositoryInterface, SearchableInterface
{
    /**
     * @param Product $product
     * @param array   $attributes
     *
     * @return Variant
     */
    public function instance(Product $product, array $attributes);

    /**
     * @param Variant      $variant
     * @param Product|null $product
     * @param array|null   $attributes
     *
     * @return void
     */
    public function fill(Variant $variant, Product $product = null, array $attributes = null);

    /**
     * @param Product    $product
     * @param array|null $attributes
     *
     * @return Variant
     */
    public function create(Product $product, array $attributes = null);

    /**
     * @param string $sku
     * @param array  $relations
     * @param array  $columns
     *
     * @return Variant
     */
    public function read($sku, array $relations = [], array $columns = ['*']);
}
