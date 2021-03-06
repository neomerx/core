<?php namespace Neomerx\Core\Models;

/**
 * @property int     id
 * @property int     id_product
 * @property int     id_related_product
 * @property Product product
 * @property Product related
 *
 * @package Neomerx\Core
 */
class ProductRelated extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'product_related';

    /** Model field name */
    const FIELD_ID                 = 'id';
    /** Model field name */
    const FIELD_ID_PRODUCT         = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_RELATED_PRODUCT = 'id_related_product';
    /** Model field name */
    const FIELD_PRODUCT            = 'product';
    /** Model field name */
    const FIELD_RELATED            = 'related';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_RELATED_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_RELATED_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT         => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_RELATED_PRODUCT => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME.
                ','.Product::FIELD_ID,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,

            self::FIELD_ID_RELATED_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Product::TABLE_NAME.','.Product::FIELD_ID,
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
    public static function withRelated()
    {
        return self::FIELD_RELATED;
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
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function related()
    {
        return $this->belongsTo(Product::class, self::FIELD_ID_RELATED_PRODUCT, Product::FIELD_ID);
    }
}
