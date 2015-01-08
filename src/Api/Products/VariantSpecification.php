<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CharacteristicValue;

class VariantSpecification
{
    use SpecificationTrait;

    private $variantModel;

    private $characteristicValueModel;

    public function __construct(Variant $variant, CharacteristicValue $characteristicValue)
    {
        $this->variantModel             = $variant;
        $this->characteristicValueModel = $characteristicValue;
    }

    /**
     * Update variant specification.
     *
     * @param Variant $variant
     * @param array  $parameters
     */
    public function updateVariantSpecification(Variant $variant, array $parameters = [])
    {
        Permissions::check($variant->product, Permission::edit());
        $this->updateSpecification($this->characteristicValueModel, $parameters, $variant);
    }

    /**
     * Make specification non variable.
     *
     * @param Variant $variant
     * @param string $valueCode
     */
    public function makeSpecificationNonVariable(Variant $variant, $valueCode)
    {
        Permissions::check($variant->product, Permission::edit());

        $valueId = $this->characteristicValueModel
            ->selectByCode($valueCode)->firstOrFail()->{CharacteristicValue::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\Specification $spec */
        $spec = $variant->specification()->where(CharacteristicValue::FIELD_ID, '=', $valueId)->firstOrFail();
        $spec->makeNonVariable();

        Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'madeSpecNonVariable', $spec));
    }
}
