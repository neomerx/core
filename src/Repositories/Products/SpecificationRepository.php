<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class SpecificationRepository extends IndexBasedResourceRepository implements SpecificationRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Specification::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        BaseProduct $base,
        CharacteristicValue $value,
        array $attributes,
        Product $product = null
    ) {
        /** @var Specification $specification */
        $specification = $this->makeModel();
        $this->fill($specification, $base, $value, $attributes, $product);
        return $specification;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Specification $specification,
        BaseProduct $base = null,
        CharacteristicValue $value = null,
        array $attributes = null,
        Product $product = null
    ) {
        $this->fillModel($specification, [
            Specification::FIELD_ID_BASE_PRODUCT         => $base,
            Specification::FIELD_ID_PRODUCT              => $product,
            Specification::FIELD_ID_CHARACTERISTIC_VALUE => $value,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function makeVariable(Specification $specification)
    {
        // Basically we want having such specification for all products. Which requires the following steps
        // 1) Get default product and all other products.
        // 2) Assign specification to default product.
        // 3) Make copy of specification for every non-default product.
        // 2-3 should be done in transaction.
        $base     = $specification->{Specification::FIELD_BASE_PRODUCT};
        $products = $base->{BaseProduct::FIELD_PRODUCTS};
        $value    = $specification->value;

        $specAttributes = $specification->attributesToArray();

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            foreach ($products as $product) {
                /** @var Product $product */
                if ($product->isDefault() === true) {
                    $specification->{Specification::FIELD_ID_PRODUCT} = $product->{Product::FIELD_ID};
                    $specification->saveOrFail();
                } else {
                    $this->instance($base, $value, $specAttributes, $product)->saveOrFail();
                }
            }

            $allExecutedOk = true;
        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @inheritdoc
     *
     * @throws \Neomerx\Core\Exceptions\LogicException
     */
    public function makeNonVariable(Specification $specification)
    {
        // This method could be called on specification that belongs to default product.
        // Technically it removes all specifications with the same characteristic (not value!)
        // from non-default products and moves specification from default product to base product
        // which is just removing relation between specification and product.

        $base    = $specification->{Specification::FIELD_BASE_PRODUCT};
        $product = $specification->{Specification::FIELD_PRODUCT};

        // check we are variable specification and belonging to default product
        if ($product === null || $product->isDefault() === false) {
            throw new LogicException();
        }

        $characteristicId = $specification->value->characteristic->{Characteristic::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // assign specification back to product
            $specification->{Specification::FIELD_ID_PRODUCT} = null;
            $specification->saveOrFail();

            // remove all other specification rows that belong to the same product and same characteristic
            /** @noinspection PhpUndefinedMethodInspection */
            $specIdsToDelete = DB::table(Specification::TABLE_NAME)->join(
                // join characteristic value on its ID
                CharacteristicValue::TABLE_NAME,
                CharacteristicValue::TABLE_NAME.'.'.CharacteristicValue::FIELD_ID,
                '=',
                Specification::TABLE_NAME.'.'.Specification::FIELD_ID_CHARACTERISTIC_VALUE
            )->where(
                // only for current product
                Specification::TABLE_NAME.'.'.Specification::FIELD_ID_BASE_PRODUCT,
                $base->{BaseProduct::FIELD_ID}
            )->where(
                // only with the same characteristic
                CharacteristicValue::TABLE_NAME.'.'. CharacteristicValue::FIELD_ID_CHARACTERISTIC,
                $characteristicId
            )->whereNotNull(
                // except specification rows which belong to product only
                Specification::TABLE_NAME.'.'.Specification::FIELD_ID_PRODUCT
            )->lists(Specification::FIELD_ID);

            if (empty($specIdsToDelete) === false) {
                /** @noinspection PhpUndefinedMethodInspection */
                DB::table(Specification::TABLE_NAME)->whereIn(Specification::FIELD_ID, $specIdsToDelete)->delete();
            }

            $allExecutedOk = true;
        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @inheritdoc
     */
    public function getMaxPosition(BaseProduct $base)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $position = $this->getUnderlyingModel()
            ->newQuery()
            ->where(Specification::FIELD_ID_BASE_PRODUCT, '=', $base->{BaseProduct::FIELD_ID})
            ->max(Specification::FIELD_POSITION);
        return $position === null ? 0 : (int)$position;
    }
}
