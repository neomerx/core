<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;

interface VariantRepositoryInterface
{
    /**
     * @param Product $product
     * @param array   $attributes
     *
     * @return Variant
     */
    public function instance(Product $product, array $attributes = null);

    /**
     * @param Variant $variant
     * @param Product $product
     * @param array   $attributes
     *
     * @return void
     */
    public function fill(Variant $variant, Product $product = null, array $attributes = null);

    /**
     * @param Product $product
     * @param array   $attributes
     *
     * @return Variant
     */
    public function create(Product $product, array $attributes = null);
}
