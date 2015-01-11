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
 */
class OrderHistory extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'order_history';

    const FIELD_ID              = 'id_order_history';
    const FIELD_ID_ORDER        = Order::FIELD_ID;
    const FIELD_ID_ORDER_STATUS = OrderStatus::FIELD_ID;
    const FIELD_CREATED_AT      = 'created_at';
    const FIELD_UPDATED_AT      = 'updated_at';
    const FIELD_STATUS          = 'status';
    const FIELD_ORDER           = 'order';

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
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_ID_ORDER,
        self::FIELD_ID_ORDER_STATUS,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ORDER        => 'required|integer|min:1|max:4294967295|exists:' . Order::TABLE_NAME,
            self::FIELD_ID_ORDER_STATUS => 'required|integer|min:1|max:4294967295|exists:' . OrderStatus::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ORDER        =>'sometimes|required|integer|min:1|max:4294967295|exists:' . Order::TABLE_NAME,
            self::FIELD_ID_ORDER_STATUS =>'sometimes|required|integer|min:1|max:4294967295|exists:' .
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
        return $this->belongsTo(OrderStatus::BIND_NAME, self::FIELD_ID_ORDER_STATUS, OrderStatus::FIELD_ID);
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
}
