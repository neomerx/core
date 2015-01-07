<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Exceptions\ValidationException;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\Specification as SpecificationModel;
use \Neomerx\Core\Models\CharacteristicValue as CharacteristicValueModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductSpecification
{
    use SpecificationTrait;

    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var SpecificationModel
     */
    private $specificationModel;

    /**
     * @var CharacteristicValueModel
     */
    private $characteristicValueModel;

    /**
     * @param Model                    $product
     * @param SpecificationModel       $specification
     * @param CharacteristicValueModel $characteristicValue
     */
    public function __construct(
        Model $product,
        SpecificationModel $specification,
        CharacteristicValueModel $characteristicValue
    ) {
        $this->productModel             = $product;
        $this->specificationModel       = $specification;
        $this->characteristicValueModel = $characteristicValue;
    }

    /**
     * Read product specification.
     *
     * @param Model $product
     *
     * @return Collection
     */
    public function showProductSpecification(Model $product)
    {
        return $this->readProductSpecification($product);
    }

    /**
     * Add characteristic values to product specification.
     *
     * @param Model $product
     * @param array  $valueCodes
     */
    public function storeProductSpecification(Model $product, array $valueCodes)
    {
        Permissions::check($product, Permission::edit());

        $valueIds = $this->characteristicValueModel
            ->selectByCodes($valueCodes)->lists(CharacteristicValueModel::FIELD_ID);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $productId = $product->{Model::FIELD_ID};
            $lastPosition = $this->specificationModel->selectMaxPosition($productId);
            $lastPosition = $lastPosition ?: 0;
            foreach ($valueIds as $valueId) {
                /** @var SpecificationModel $spec */
                /** @noinspection PhpUndefinedMethodInspection */
                $spec = App::make(SpecificationModel::BIND_NAME);
                $spec->fill([
                    SpecificationModel::FIELD_ID_PRODUCT              => $productId,
                    SpecificationModel::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                    SpecificationModel::FIELD_POSITION                => ++$lastPosition,
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
     * @param Model $product
     * @param array $valueCodes
     */
    public function destroyProductSpecification(Model $product, array $valueCodes)
    {
        Permissions::check($product, Permission::edit());

        $valueIds = $this->characteristicValueModel
            ->selectByCodes($valueCodes)->lists(CharacteristicValueModel::FIELD_ID);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @noinspection PhpUndefinedMethodInspection */
            $specs = $product->specification()->whereIn(CharacteristicValueModel::FIELD_ID, $valueIds)->get();
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
     * @param Model $product
     * @param array $parameters
     */
    public function updateProductSpecification(Model $product, array $parameters = [])
    {
        Permissions::check($product, Permission::edit());
        $this->updateSpecification($this->characteristicValueModel, $parameters, $product);
    }

    /**
     * Make specification variable.
     *
     * @param Model  $product
     * @param string $valueCode
     */
    public function makeSpecificationVariable(Model $product, $valueCode)
    {
        Permissions::check($product, Permission::edit());

        $valueId = $this->characteristicValueModel->selectByCode($valueCode)
            ->firstOrFail()->{CharacteristicValueModel::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var SpecificationModel $spec */
        $spec = $product->specification()->where(CharacteristicValueModel::FIELD_ID, '=', $valueId)->firstOrFail();
        $spec->makeVariable();

        Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'madeSpecVariable', $spec));
    }
}
