<?php namespace Neomerx\Core\Models;

/**
 * @property int           id_order_details
 * @property int           id_order
 * @property int           id_store
 * @property int           id_shipping_order
 * @property int           id_variant
 * @property float         price_wo_tax
 * @property int           quantity
 * @property Order         order
 * @property ShippingOrder shipping
 * @property Variant       variant
 */
class OrderDetails extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'order_details';

    const FIELD_ID                = 'id_order_details';
    const FIELD_ID_ORDER          = Order::FIELD_ID;
    const FIELD_ID_SHIPPING_ORDER = ShippingOrder::FIELD_ID;
    const FIELD_ID_VARIANT        = Variant::FIELD_ID;
    const FIELD_PRICE_WO_TAX      = 'price_wo_tax';
    const FIELD_QUANTITY          = 'quantity';
    const FIELD_ORDER             = 'order';
    const FIELD_SHIPPING          = 'shipping';
    const FIELD_ITEM              = 'item';

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
        self::FIELD_ID_VARIANT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ORDER,
        self::FIELD_ID_SHIPPING_ORDER,
        self::FIELD_ID_VARIANT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ORDER   => 'required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,

            self::FIELD_ID_SHIPPING_ORDER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ShippingOrder::TABLE_NAME,

            self::FIELD_PRICE_WO_TAX => 'required|numeric|min:0',
            self::FIELD_QUANTITY     => 'required|integer|min:1|max:65535',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ORDER   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,

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
        return $this->belongsTo(Order::BIND_NAME, self::FIELD_ID_ORDER, Order::FIELD_ID);
    }

    /**
     * Relation to shipping order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipping()
    {
        return $this->belongsTo(ShippingOrder::BIND_NAME, self::FIELD_ID_SHIPPING_ORDER, ShippingOrder::FIELD_ID);
    }

    /**
     * Relation to variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::BIND_NAME, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }
}
