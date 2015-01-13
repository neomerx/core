<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\CharacteristicValue;

interface SpecificationRepositoryInterface
{
    /**
     * @param Product             $product
     * @param Variant             $variant
     * @param CharacteristicValue $value
     * @param array               $attributes
     *
     * @return Specification
     */
    public function instance(
        Product $product = null,
        Variant $variant = null,
        CharacteristicValue $value = null,
        array $attributes = null
    );

    /**
     * @param Specification       $specification
     * @param Product             $product
     * @param Variant             $variant
     * @param CharacteristicValue $value
     * @param array               $attributes
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
