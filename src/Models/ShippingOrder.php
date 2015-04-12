<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int                 id_shipping_order
 * @property      int                 id_order
 * @property      int                 id_carrier
 * @property      int                 id_shipping_order_status
 * @property      string              tracking_number
 * @property-read Carbon              created_at
 * @property-read Carbon              updated_at
 * @property      Carrier             carrier
 * @property      ShippingOrderStatus status
 *
 * @package Neomerx\Core
 */
class ShippingOrder extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'shipping_orders';

    /** Model field max length */
    const TRACKING_NUMBER_MAX = 20;

    /** Model field name */
    const FIELD_ID                       = 'id_shipping_order';
    /** Model field name */
    const FIELD_ID_CARRIER               = Carrier::FIELD_ID;
    /** Model field name */
    const FIELD_ID_SHIPPING_ORDER_STATUS = ShippingOrderStatus::FIELD_ID;
    /** Model field name */
    const FIELD_TRACKING_NUMBER          = 'tracking_number';
    /** Model field name */
    const FIELD_CREATED_AT               = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT               = 'updated_at';
    /** Model field name */
    const FIELD_ORDER                    = 'order';
    /** Model field name */
    const FIELD_CARRIER                  = 'carrier';
    /** Model field name */
    const FIELD_STATUS                   = 'status';

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
    public $timestamps = true;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_TRACKING_NUMBER,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_SHIPPING_ORDER_STATUS,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_SHIPPING_ORDER_STATUS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER      => 'required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,
            self::FIELD_TRACKING_NUMBER => 'max:'.self::TRACKING_NUMBER_MAX,

            self::FIELD_ID_SHIPPING_ORDER_STATUS => 'required|integer|min:1|max:4294967295|exists:'.
                ShippingOrderStatus::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,

            self::FIELD_TRACKING_NUMBER => 'max:'.self::TRACKING_NUMBER_MAX,

            self::FIELD_ID_SHIPPING_ORDER_STATUS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ShippingOrderStatus::TABLE_NAME,
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withCarrier()
    {
        return  self::FIELD_CARRIER.'.'.Carrier::FIELD_PROPERTIES.'.'.CarrierProperties::FIELD_LANGUAGE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withStatus()
    {
        return self::FIELD_STATUS;
    }

    /**
     * Relation to carrier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class, self::FIELD_ID_CARRIER, Carrier::FIELD_ID);
    }

    /**
     * Relation to shipment status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(
            ShippingOrderStatus::class,
            self::FIELD_ID_SHIPPING_ORDER_STATUS,
            ShippingOrderStatus::FIELD_ID
        );
    }
}
