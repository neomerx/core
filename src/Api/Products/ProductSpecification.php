<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CharacteristicValue;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductSpecification
{
    use SpecificationTrait;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var Specification
     */
    private $specificationModel;

    /**
     * @var CharacteristicValue
     */
    private $characteristicValueModel;

    /**
     * @param Product             $product
     * @param Specification       $specification
     * @param CharacteristicValue $characteristicValue
     */
    public function __construct(
        Product $product,
        Specification $specification,
        CharacteristicValue $characteristicValue
    ) {
        $this->productModel             = $product;
        $this->specificationModel       = $specification;
        $this->characteristicValueModel = $characteristicValue;
    }

    /**
     * Read product specification.
     *
     * @param Product $product
     *
     * @return Collection
     */
    public function showProductSpecification(Product $product)
    {
        return $this->readProductSpecification($product);
    }

    /**
     * Add characteristic values to product specification.
     *
     * @param Product $product
     * @param array  $valueCodes
     */
    public function storeProductSpecification(Product $product, array $valueCodes)
    {
        Permissions::check($product, Permission::edit());

        $valueIds = $this->characteristicValueModel
            ->selectByCodes($valueCodes)->lists(CharacteristicValue::FIELD_ID);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $productId = $product->{Product::FIELD_ID};
            $lastPosition = $this->specificationModel->selectMaxPosition($productId);
            $lastPosition = $lastPosition ?: 0;
            foreach ($valueIds as $valueId) {
                /** @var \Neomerx\Core\Models\Specification $spec */
                /** @noinspection PhpUndefinedMethodInspection */
                $spec = App::make(Specification::BIND_NAME);
                $spec->fill([
                    Specification::FIELD_ID_PRODUCT              => $productId,
                    Specification::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                    Specification::FIELD_POSITION                => ++$lastPosition,
                ]);
                /** @noinspection PhpUndefinedMethodInspection */
                $spec->save() ?: S\throwEx(new ValidationException($spec->getValidator()));

                Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'addedSpecification', $spec));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

    }

    /**
     * Remove characteristic values from product specification.
     *
     * @param Product $product
     * @param array $valueCodes
     */
    public function destroyProductSpecification(Product $product, array $valueCodes)
    {
        Permissions::check($product, Permission::edit());

        $valueIds = $this->characteristicValueModel
            ->selectByCodes($valueCodes)->lists(CharacteristicValue::FIELD_ID);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @noinspection PhpUndefinedMethodInspection */
            $specs = $product->specification()->whereIn(CharacteristicValue::FIELD_ID, $valueIds)->get();
            foreach ($specs as $spec) {
                /** @noinspection PhpUndefinedMethodInspection */
                $spec->deleteOrFail();

                Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'deletedSpecification', $spec));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

    }

    /**
     * Update characteristic values in product specification.
     *
     * @param Product $product
     * @param array $parameters
     */
    public function updateProductSpecification(Product $product, array $parameters = [])
    {
        Permissions::check($product, Permission::edit());
        $this->updateSpecification($this->characteristicValueModel, $parameters, $product);
    }

    /**
     * Make specification variable.
     *
     * @param Product  $product
     * @param string $valueCode
     */
    public function makeSpecificationVariable(Product $product, $valueCode)
    {
        Permissions::check($product, Permission::edit());

        $valueId = $this->characteristicValueModel->selectByCode($valueCode)
            ->firstOrFail()->{CharacteristicValue::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Neomerx\Core\Models\Specification $spec */
        $spec = $product->specification()->where(CharacteristicValue::FIELD_ID, '=', $valueId)->firstOrFail();
        $spec->makeVariable();

        Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'madeSpecVariable', $spec));
    }
}
