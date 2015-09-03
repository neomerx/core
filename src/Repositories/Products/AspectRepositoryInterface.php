<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Aspect;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface AspectRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct   $base
     * @param FeatureValue  $value
     * @param array         $attributes
     * @param Nullable|null $product Product
     *
     * @return Aspect
     */
    public function createWithObjects(
        BaseProduct $base,
        FeatureValue $value,
        array $attributes,
        Nullable $product = null
    );

    /**
     * @param int           $baseId
     * @param int           $valueId
     * @param array         $attributes
     * @param Nullable|null $productId
     *
     * @return Aspect
     */
    public function create(
        $baseId,
        $valueId,
        array $attributes,
        Nullable $productId = null
    );

    /**
     * @param Aspect            $aspect
     * @param BaseProduct|null  $base
     * @param FeatureValue|null $value
     * @param array|null        $attributes
     * @param Nullable|null     $product Product
     *
     * @return void
     */
    public function updateWithObjects(
        Aspect $aspect,
        BaseProduct $base = null,
        FeatureValue $value = null,
        array $attributes = null,
        Nullable $product = null
    );

    /**
     * @param Aspect        $aspect
     * @param int|null      $baseId
     * @param int|null      $valueId
     * @param array|null    $attributes
     * @param Nullable|null $productId
     *
     * @return void
     */
    public function update(
        Aspect $aspect,
        $baseId = null,
        $valueId = null,
        array $attributes = null,
        Nullable $productId = null
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
