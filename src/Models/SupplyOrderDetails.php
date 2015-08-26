<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;

/**
 * @property int         id_supply_order_details
 * @property int         id_supply_order
 * @property int         id_product
 * @property int         price_wo_tax
 * @property int         quantity
 * @property float       discount_rate
 * @property float       tax_rate
 * @property SupplyOrder supplyOrder
 * @property Product     product
 *
 * @package Neomerx\Core
 */
class SupplyOrderDetails extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'supply_order_details';

    /** Model field name */
    const FIELD_ID              = 'id_supply_order_details';
    /** Model field name */
    const FIELD_ID_SUPPLY_ORDER = 'id_supply_order';
    /** Model field name */
    const FIELD_ID_PRODUCT      = Product::FIELD_ID;
    /** Model field name */
    const FIELD_PRICE_WO_TAX    = 'price_wo_tax';
    /** Model field name */
    const FIELD_QUANTITY        = 'quantity';
    /** Model field name */
    const FIELD_DISCOUNT_RATE   = 'discount_rate';
    /** Model field name */
    const FIELD_TAX_RATE        = 'tax_rate';
    /** Model field name */
    const FIELD_SUPPLY_ORDER    = 'supplyOrder';
    /** Model field name */
    const FIELD_PRODUCT         = 'product';

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
        self::FIELD_PRICE_WO_TAX,
        self::FIELD_QUANTITY,
        self::FIELD_DISCOUNT_RATE,
        self::FIELD_TAX_RATE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_SUPPLY_ORDER,
        self::FIELD_ID_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_SUPPLY_ORDER => 'required|integer|min:1|max:4294967295|exists:'.SupplyOrder::TABLE_NAME,
            self::FIELD_ID_PRODUCT      => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_PRICE_WO_TAX    => 'required|integer|min:0',
            self::FIELD_QUANTITY        => 'required|integer|min:1|max:4294967295',
            self::FIELD_DISCOUNT_RATE   => 'sometimes|required|numeric|min:0|max:100',
            self::FIELD_TAX_RATE        => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_SUPPLY_ORDER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                SupplyOrder::TABLE_NAME,

            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,

            self::FIELD_PRICE_WO_TAX  => 'sometimes|required|integer|min:0',
            self::FIELD_QUANTITY      => 'sometimes|required|integer|min:1|max:4294967295',
            self::FIELD_DISCOUNT_RATE => 'sometimes|required|numeric|min:0|max:100',
            self::FIELD_TAX_RATE      => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * Relation to supply order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyOrder()
    {
        return $this->belongsTo(SupplyOrder::class, self::FIELD_ID_SUPPLY_ORDER, SupplyOrder::FIELD_ID);
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
}
