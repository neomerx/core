<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SpecificationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param BaseProduct         $base
     * @param CharacteristicValue $value
     * @param array               $attributes
     * @param Product|null        $product
     *
     * @return Specification
     */
    public function instance(
        BaseProduct $base,
        CharacteristicValue $value,
        array $attributes,
        Product $product = null
    );

    /**
     * @param Specification            $specification
     * @param BaseProduct|null         $base
     * @param CharacteristicValue|null $value
     * @param array|null               $attributes
     * @param Product|null             $product
     *
     * @return void
     */
    public function fill(
        Specification $specification,
        BaseProduct $base = null,
        CharacteristicValue $value = null,
        array $attributes = null,
        Product $product = null
    );

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return Specification
     */
    public function read($index, array $relations = [], array $columns = ['*']);

    /**
     * @param Specification $specification
     *
     * @return void
     */
    public function makeVariable(Specification $specification);

    /**
     * @param Specification $specification
     *
     * @return void
     */
    public function makeNonVariable(Specification $specification);

    /**
     * Get max/last specification position for product.
     *
     * @param BaseProduct $base
     *
     * @return int
     */
    public function getMaxPosition(BaseProduct $base);
}
