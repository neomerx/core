<?php namespace Neomerx\Core\Models;

/**
 * @property int           id_order_details
 * @property int           id_order
 * @property int           id_store
 * @property int           id_shipping_order
 * @property int           id_product
 * @property float         price_wo_tax
 * @property int           quantity
 * @property Order         order
 * @property ShippingOrder shipping
 * @property Product       product
 *
 * @package Neomerx\Core
 */
class OrderDetails extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'order_details';

    /** Model field name */
    const FIELD_ID                = 'id_order_details';
    /** Model field name */
    const FIELD_ID_ORDER          = Order::FIELD_ID;
    /** Model field name */
    const FIELD_ID_SHIPPING_ORDER = ShippingOrder::FIELD_ID;
    /** Model field name */
    const FIELD_ID_PRODUCT        = Product::FIELD_ID;
    /** Model field name */
    const FIELD_PRICE_WO_TAX      = 'price_wo_tax';
    /** Model field name */
    const FIELD_QUANTITY          = 'quantity';
    /** Model field name */
    const FIELD_ORDER             = 'order';
    /** Model field name */
    const FIELD_SHIPPING          = 'shipping';
    /** Model field name */
    const FIELD_PRODUCT           = 'product';

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
        self::FIELD_ID_ORDER,
        self::FIELD_ID_SHIPPING_ORDER,
        self::FIELD_ID_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ORDER   => 'required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_PRODUCT => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,

            self::FIELD_ID_SHIPPING_ORDER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ShippingOrder::TABLE_NAME,

            self::FIELD_PRICE_WO_TAX => 'required|numeric|min:0',
            self::FIELD_QUANTITY     => 'required|integer|min:1|max:65535',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ORDER   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,

            self::FIELD_ID_SHIPPING_ORDER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ShippingOrder::TABLE_NAME,

            self::FIELD_PRICE_WO_TAX => 'sometimes|required|numeric|min:0',
            self::FIELD_QUANTITY     => 'sometimes|required|integer|min:1|max:65535',
        ];
    }

    /**
     * Relation to order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, self::FIELD_ID_ORDER, Order::FIELD_ID);
    }

    /**
     * Relation to shipping order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipping()
    {
        return $this->belongsTo(ShippingOrder::class, self::FIELD_ID_SHIPPING_ORDER, ShippingOrder::FIELD_ID);
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
