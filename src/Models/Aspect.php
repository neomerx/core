<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;

/**
 * @property int          id_aspect
 * @property int          id_base_product
 * @property int          id_product
 * @property int          id_feature_value
 * @property int          position
 * @property BaseProduct  baseProduct
 * @property Product      product
 * @property FeatureValue value
 *
 * @package Neomerx\Core
 */
class Aspect extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'aspects';

    /** Model field name */
    const FIELD_ID              = 'id_aspect';
    /** Model field name */
    const FIELD_ID_BASE_PRODUCT = BaseProduct::FIELD_ID;
    /** Model field name */
    const FIELD_ID_PRODUCT      = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_VALUE        = FeatureValue::FIELD_ID;
    /** Model field name */
    const FIELD_POSITION        = 'position';
    /** Model field name */
    const FIELD_BASE_PRODUCT    = 'baseProduct';
    /** Model field name */
    const FIELD_PRODUCT         = 'product';
    /** Model field name */
    const FIELD_VALUE           = 'value';
    /** Model field name */
    const FIELD_IS_SHARED       = 'isShared';

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
        self::FIELD_ID_BASE_PRODUCT,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VALUE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_BASE_PRODUCT,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VALUE,
    ];

    /**
     * @inheritdoc
     */
    protected $appends = [
        self::FIELD_IS_SHARED,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_BASE_PRODUCT => 'required|integer|min:1|max:4294967295|exists:'.BaseProduct::TABLE_NAME,

            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_POSITION   => 'required|numeric|min:0|max:255',
            self::FIELD_ID_VALUE   => 'required|integer|min:1|max:4294967295|exists:'.FeatureValue::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_BASE_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                BaseProduct::TABLE_NAME,

            self::FIELD_ID_PRODUCT => 'sometimes|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_POSITION   => 'sometimes|required|numeric|min:0|max:255',

            self::FIELD_ID_VALUE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.FeatureValue::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withBaseProduct()
    {
        return self::FIELD_BASE_PRODUCT;
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
    public static function withValue()
    {
        return self::FIELD_VALUE;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSharedAttribute()
    {
        return empty($this->getAttributeValue(self::FIELD_ID_PRODUCT));
    }

    /**
     * Relation to base product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function baseProduct()
    {
        return $this->belongsTo(BaseProduct::class, self::FIELD_ID_BASE_PRODUCT, BaseProduct::FIELD_ID);
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
     * Relation to feature value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function value()
    {
        return $this->belongsTo(FeatureValue::class, self::FIELD_ID_VALUE, FeatureValue::FIELD_ID);
    }
}
