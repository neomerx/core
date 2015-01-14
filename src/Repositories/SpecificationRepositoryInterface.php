<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\CharacteristicValue;

interface SpecificationRepositoryInterface extends SearchableInterface
{
    /**
     * @param Product             $product
     * @param CharacteristicValue $value
     * @param Variant|null        $variant
     * @param array|null          $attributes
     *
     * @return Specification
     */
    public function instance(
        Product $product,
        CharacteristicValue $value,
        Variant $variant = null,
        array $attributes = null
    );

    /**
     * @param Specification            $specification
     * @param Product|null             $product
     * @param Variant|null             $variant
     * @param CharacteristicValue|null $value
     * @param array|null               $attributes
     *
     * @return void
     */
    public function fill(
        Specification $specification,
        Product $product = null,
        Variant $variant = null,
        CharacteristicValue $value = null,
        array $attributes = null
    );

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
