<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Exceptions\LogicException;

/**
 * @property int                 id_specification
 * @property int                 id_product
 * @property int                 id_variant
 * @property int                 id_characteristic_value
 * @property int                 position
 * @property Product             product
 * @property Variant             variant
 * @property CharacteristicValue value
 */
class Specification extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'specifications';

    const FIELD_ID                      = 'id_specification';
    const FIELD_ID_PRODUCT              = Product::FIELD_ID;
    const FIELD_ID_VARIANT              = Variant::FIELD_ID;
    const FIELD_ID_CHARACTERISTIC_VALUE = CharacteristicValue::FIELD_ID;
    const FIELD_POSITION                = 'position';
    const FIELD_PRODUCT                 = 'product';
    const FIELD_VARIANT                 = 'variant';
    const FIELD_VALUE                   = 'value';

    /**
     * {@inheritdoc}
     */
    protected $table = self::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_CHARACTERISTIC_VALUE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_CHARACTERISTIC_VALUE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT  => 'required|integer|min:1|max:4294967295|exists:' . Product::TABLE_NAME,
            self::FIELD_ID_VARIANT  => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Variant::TABLE_NAME,
            self::FIELD_POSITION    => 'required|numeric|min:0|max:255',

            self::FIELD_ID_CHARACTERISTIC_VALUE => 'required|integer|min:1|max:4294967295|exists:' .
                CharacteristicValue::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|integer|min:1|max:4294967295|exists:' . Variant::TABLE_NAME,
            self::FIELD_POSITION   => 'sometimes|required|numeric|min:0|max:255',

            self::FIELD_ID_CHARACTERISTIC_VALUE => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                CharacteristicValue::TABLE_NAME,
        ];
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::BIND_NAME, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to product variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::BIND_NAME, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }

    /**
     * Relation to characteristic value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function value()
    {
        return $this->belongsTo(
            CharacteristicValue::BIND_NAME,
            self::FIELD_ID_CHARACTERISTIC_VALUE,
            CharacteristicValue::FIELD_ID
        );
    }

    /**
     * Select max/last specification position for product.
     *
     * @param int $productId
     *
     * @return mixed
     */
    public function selectMaxPosition($productId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->newQuery()->where(self::FIELD_ID_PRODUCT, '=', $productId)->max(self::FIELD_POSITION);
    }

    /**
     * Make specification available in product variants.
     */
    public function makeVariable()
    {
        // Basically we want having such specification for all product variants. Which requires the following steps
        // 1) Get default variant and all other product variants.
        // 2) Assign specification to default variant.
        // 3) Make copy of specification for every non-default variant.
        // 2-3 should be done in transaction.
        $product  = $this->product;
        $variants = $product->variants;
        $value    = $this->value;
        $specData = $this->attributesToArray();

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($variants as $variant) {
                if (Variant::isDefault($variant, $product)) {
                    // 2)
                    $this->{self::FIELD_ID_VARIANT} = $variant->{Variant::FIELD_ID};
                    $this->saveOrFail();
                } else {
                    // 3)
                    /** @noinspection PhpUndefinedMethodInspection */
                    /** @var Specification $copySpec */
                    $copySpec = App::make(self::BIND_NAME);
                    $copySpec->fillModel($product, $variant, $value, $specData)->saveOrFail();
                }
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * Remove specification from product variant and leave default only in product.
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function makeNonVariable()
    {
        // This method could be called on specification that belongs to default product variant.
        // Technically it removes all specifications with the same characteristic (not value!)
        // from non-default variants and moves specification from default variant to product
        // which is just removing relation between specification and variant.

        $product = $this->product;
        $variant = $this->variant;

        // check we are variable specification and belonging to default variant
        $variant !== null ?: S\throwEx(new LogicException());
        Variant::isDefault($variant, $product) ?: S\throwEx(new LogicException());

        $characteristicId = $this->value->characteristic->{Characteristic::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // assign specification back to product
            $this->{self::FIELD_ID_VARIANT} = null;
            $this->saveOrFail();

            // remove all other specifications that belong to the same product and same characteristic
            /** @noinspection PhpUndefinedMethodInspection */
            $specIdsToDelete = DB::table(self::TABLE_NAME)
                // join characteristic value on its ID
                ->join(
                    CharacteristicValue::TABLE_NAME,
                    CharacteristicValue::TABLE_NAME . '.' . CharacteristicValue::FIELD_ID,
                    '=',
                    self::TABLE_NAME . '.' . self::FIELD_ID_CHARACTERISTIC_VALUE
                )
                // only for current product
                ->where(self::TABLE_NAME . '.' . self::FIELD_ID_PRODUCT, '=', $product->{Product::FIELD_ID})
                // only with the same characteristic (not value!!!)
                ->where(CharacteristicValue::TABLE_NAME . '.' . Characteristic::FIELD_ID, '=', $characteristicId)
                // except specification that belongs to product already
                ->whereNotNull(self::TABLE_NAME . '.' . self::FIELD_ID_VARIANT)
                ->lists(self::FIELD_ID);
            /** @noinspection PhpUndefinedMethodInspection */
            empty($specIdsToDelete) ?: DB::table(self::TABLE_NAME)->whereIn(self::FIELD_ID, $specIdsToDelete)->delete();

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @param Product             $product
     * @param Variant             $variant
     * @param CharacteristicValue $value
     * @param array               $attributes
     *
     * @return $this
     */
    public function fillModel(
        Product $product = null,
        Variant $variant = null,
        CharacteristicValue $value = null,
        array $attributes = null
    ) {
        $data = [
            self::FIELD_ID_PRODUCT              => $product,
            self::FIELD_ID_VARIANT              => $variant,
            self::FIELD_ID_CHARACTERISTIC_VALUE => $value,
        ];

        empty($attributes) ?: $this->fill($attributes);
        foreach ($data as $attribute => $model) {
            /** @var BaseModel $model */
            $model === null ?: $this->setAttribute($attribute, $model->getKey());
        }

        return $this;
    }
}
