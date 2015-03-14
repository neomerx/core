<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Product;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class SpecificationRepository extends IndexBasedResourceRepository implements SpecificationRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Specification::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Product $product,
        CharacteristicValue $value,
        array $attributes,
        Variant $variant = null
    ) {
        /** @var \Neomerx\Core\Models\Specification $specification */
        $specification = $this->makeModel();
        $this->fill($specification, $product, $value, $attributes, $variant);
        return $specification;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Specification $specification,
        Product $product = null,
        CharacteristicValue $value = null,
        array $attributes = null,
        Variant $variant = null
    ) {
        $this->fillModel($specification, [
            Specification::FIELD_ID_PRODUCT              => $product,
            Specification::FIELD_ID_VARIANT              => $variant,
            Specification::FIELD_ID_CHARACTERISTIC_VALUE => $value,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function makeVariable(Specification $specification)
    {
        // Basically we want having such specification for all product variants. Which requires the following steps
        // 1) Get default variant and all other product variants.
        // 2) Assign specification to default variant.
        // 3) Make copy of specification for every non-default variant.
        // 2-3 should be done in transaction.
        $product  = $specification->product;
        $variants = $product->variants;
        $value    = $specification->value;

        $specAttributes = $specification->attributesToArray();

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            foreach ($variants as $variant) {
                /** @var Variant $variant */
                if ($variant->isDefault() === true) {
                    $specification->{Specification::FIELD_ID_VARIANT} = $variant->{Variant::FIELD_ID};
                    $specification->saveOrFail();
                } else {
                    $this->instance($product, $value, $specAttributes, $variant)->saveOrFail();
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
        // This method could be called on specification that belongs to default product variant.
        // Technically it removes all specifications with the same characteristic (not value!)
        // from non-default variants and moves specification from default variant to product
        // which is just removing relation between specification and variant.

        $product = $specification->product;
        $variant = $specification->variant;

        // check we are variable specification and belonging to default variant
        if ($variant === null || $variant->isDefault() === false) {
            throw new LogicException();
        }

        $characteristicId = $specification->value->characteristic->{Characteristic::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // assign specification back to product
            $specification->{Specification::FIELD_ID_VARIANT} = null;
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
                Specification::TABLE_NAME.'.'.Specification::FIELD_ID_PRODUCT,
                $product->{Product::FIELD_ID}
            )->where(
                // only with the same characteristic
                CharacteristicValue::TABLE_NAME.'.'. CharacteristicValue::FIELD_ID_CHARACTERISTIC,
                $characteristicId
            )->whereNotNull(
                // except specification rows which belong to product only
                Specification::TABLE_NAME.'.'.Specification::FIELD_ID_VARIANT
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
    public function getMaxPosition(Product $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $position = $this->getUnderlyingModel()
            ->newQuery()
            ->where(Specification::FIELD_ID_PRODUCT, '=', $product->{Product::FIELD_ID})
            ->max(Specification::FIELD_POSITION);
        return $position === null ? 0 : (int)$position;
    }
}
