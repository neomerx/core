<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;

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
            self::FIELD_ID_PRODUCT  => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_VARIANT  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
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
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
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
}
