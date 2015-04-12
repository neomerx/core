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
 *
 * @package Neomerx\Core
 */
class Specification extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'specifications';

    /** Model field name */
    const FIELD_ID                      = 'id_specification';
    /** Model field name */
    const FIELD_ID_PRODUCT              = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_VARIANT              = Variant::FIELD_ID;
    /** Model field name */
    const FIELD_ID_CHARACTERISTIC_VALUE = CharacteristicValue::FIELD_ID;
    /** Model field name */
    const FIELD_POSITION                = 'position';
    /** Model field name */
    const FIELD_PRODUCT                 = 'product';
    /** Model field name */
    const FIELD_VARIANT                 = 'variant';
    /** Model field name */
    const FIELD_VALUE                   = 'value';

    /**
     * @inheritdoc
     */
    protected $table = self::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * @inheritdoc
     */
    public $incrementing = true;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_POSITION,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_CHARACTERISTIC_VALUE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_CHARACTERISTIC_VALUE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT  => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_VARIANT  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_POSITION    => 'required|numeric|min:0|max:255',

            self::FIELD_ID_CHARACTERISTIC_VALUE => 'required|integer|min:1|max:4294967295|exists:'.
                CharacteristicValue::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_POSITION   => 'sometimes|required|numeric|min:0|max:255',

            self::FIELD_ID_CHARACTERISTIC_VALUE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CharacteristicValue::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withProduct()
    {
        return self::FIELD_PRODUCT;
    }

    /**
     * @return string
     */
    public static function withVariant()
    {
        return self::FIELD_VARIANT;
    }

    /**
     * @return string
     */
    public static function withValue()
    {
        return self::FIELD_VALUE;
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to product variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }

    /**
     * Relation to characteristic value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function value()
    {
        return $this->belongsTo(
            CharacteristicValue::class,
            self::FIELD_ID_CHARACTERISTIC_VALUE,
            CharacteristicValue::FIELD_ID
        );
    }
}
