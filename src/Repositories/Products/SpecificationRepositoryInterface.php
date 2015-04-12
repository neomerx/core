<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SpecificationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Product             $product
     * @param CharacteristicValue $value
     * @param array               $attributes
     * @param Variant|null        $variant
     *
     * @return Specification
     */
    public function instance(
        Product $product,
        CharacteristicValue $value,
        array $attributes,
        Variant $variant = null
    );

    /**
     * @param Specification            $specification
     * @param Product|null             $product
     * @param CharacteristicValue|null $value
     * @param array|null               $attributes
     * @param Variant|null             $variant
     *
     * @return void
     */
    public function fill(
        Specification $specification,
        Product $product = null,
        CharacteristicValue $value = null,
        array $attributes = null,
        Variant $variant = null
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
     * @param Product $product
     *
     * @return int
     */
    public function getMaxPosition(Product $product);
}
