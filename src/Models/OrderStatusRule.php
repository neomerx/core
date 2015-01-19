<?php namespace Neomerx\Core\Models;

/**
 * @property int         id_order_status_rule
 * @property int         id_order_status_from
 * @property int         id_order_status_to
 * @property OrderStatus canChangeFrom
 * @property OrderStatus canChangeTo
 */
class OrderStatusRule extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'order_status_rules';

    const FIELD_ID                   = 'id_order_status_rule';
    const FIELD_ID_ORDER_STATUS_FROM = 'id_order_status_from';
    const FIELD_ID_ORDER_STATUS_TO   = 'id_order_status_to';
    const FIELD_CAN_CHANGE_FROM      = 'canChangeFrom';
    const FIELD_CAN_CHANGE_TO        = 'canChangeTo';

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
    protected $fillable = [
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_ORDER_STATUS_FROM,
        self::FIELD_ID_ORDER_STATUS_TO,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ORDER_STATUS_FROM,
        self::FIELD_ID_ORDER_STATUS_TO,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ORDER_STATUS_FROM => 'required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME.','.OrderStatus::FIELD_ID,

            self::FIELD_ID_ORDER_STATUS_TO   => 'required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME.','.OrderStatus::FIELD_ID,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ORDER_STATUS_FROM => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME.','.OrderStatus::FIELD_ID,

            self::FIELD_ID_ORDER_STATUS_TO   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME.','.OrderStatus::FIELD_ID,
        ];
    }

    /**
     * Relation to order status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function canChangeFrom()
    {
        return $this->belongsTo(OrderStatus::BIND_NAME, self::FIELD_ID_ORDER_STATUS_FROM, OrderStatus::FIELD_ID);
    }

    /**
     * Relation to order status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function canChangeTo()
    {
        return $this->belongsTo(OrderStatus::BIND_NAME, self::FIELD_ID_ORDER_STATUS_TO, OrderStatus::FIELD_ID);
    }
}
