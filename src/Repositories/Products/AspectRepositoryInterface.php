<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Aspect;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface AspectRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct  $base
     * @param FeatureValue $value
     * @param array        $attributes
     * @param Product|null $product
     *
     * @return Aspect
     */
    public function instance(
        BaseProduct $base,
        FeatureValue $value,
        array $attributes,
        Product $product = null
    );

    /**
     * @param Aspect            $aspect
     * @param BaseProduct|null  $base
     * @param FeatureValue|null $value
     * @param array|null        $attributes
     * @param Product|null      $product
     *
     * @return void
     */
    public function fill(
        Aspect $aspect,
        BaseProduct $base = null,
        FeatureValue $value = null,
        array $attributes = null,
        Product $product = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return Aspect
     */
    public function read($index, array $relations = [], array $columns = ['*']);

    /**
     * @param Aspect $aspect
     *
     * @return void
     */
    public function makeVariable(Aspect $aspect);

    /**
     * @param Aspect $aspect
     *
     * @return void
     */
    public function makeNonVariable(Aspect $aspect);

    /**
     * Get max/last aspect position for product.
     *
     * @param BaseProduct $base
     *
     * @return int
     */
    public function getMaxPosition(BaseProduct $base);
}
