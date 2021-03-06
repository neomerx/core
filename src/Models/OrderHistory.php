<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int         id_order_history
 * @property      int         id_order
 * @property      int         id_order_status
 * @property-read Carbon      created_at
 * @property-read Carbon      updated_at
 * @property      OrderStatus status
 * @property      Order       order
 *
 * @package Neomerx\Core
 */
class OrderHistory extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'order_history';

    /** Model field name */
    const FIELD_ID              = 'id_order_history';
    /** Model field name */
    const FIELD_ID_ORDER        = Order::FIELD_ID;
    /** Model field name */
    const FIELD_ID_ORDER_STATUS = OrderStatus::FIELD_ID;
    /** Model field name */
    const FIELD_CREATED_AT      = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT      = 'updated_at';
    /** Model field name */
    const FIELD_STATUS          = 'status';
    /** Model field name */
    const FIELD_ORDER           = 'order';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_ORDER_STATUS,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ORDER,
        self::FIELD_ID_ORDER_STATUS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ORDER        => 'required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_ORDER_STATUS => 'required|integer|min:1|max:4294967295|exists:'.OrderStatus::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ORDER        => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
            self::FIELD_ID_ORDER_STATUS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME,
        ];
    }

    /**
     * Relation to order status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, self::FIELD_ID_ORDER_STATUS, OrderStatus::FIELD_ID);
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
}
