<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Models\Specification as SpecificationModel;
use \Neomerx\Core\Models\CharacteristicValue as CharacteristicValueModel;

class VariantSpecification
{
    use SpecificationTrait;

    private $variantModel;

    private $characteristicValueModel;

    public function __construct(VariantModel $variant, CharacteristicValueModel $characteristicValue)
    {
        $this->variantModel             = $variant;
        $this->characteristicValueModel = $characteristicValue;
    }

    /**
     * Update variant specification.
     *
     * @param VariantModel $variant
     * @param array  $parameters
     */
    public function updateVariantSpecification(VariantModel $variant, array $parameters = [])
    {
        Permissions::check($variant->product, Permission::edit());
        $this->updateSpecification($this->characteristicValueModel, $parameters, $variant);
    }

    /**
     * Make specification non variable.
     *
     * @param VariantModel $variant
     * @param string $valueCode
     */
    public function makeSpecificationNonVariable(VariantModel $variant, $valueCode)
    {
        Permissions::check($variant->product, Permission::edit());

        $valueId = $this->characteristicValueModel
            ->selectByCode($valueCode)->firstOrFail()->{CharacteristicValueModel::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var SpecificationModel $spec */
        $spec = $variant->specification()->where(CharacteristicValueModel::FIELD_ID, '=', $valueId)->firstOrFail();
        $spec->makeNonVariable();

        Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'madeSpecNonVariable', $spec));
    }
}
