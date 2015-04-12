<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_order_status
 * @property string     name
 * @property string     code
 * @property Collection orders
 * @property Collection availableStatuses
 *
 * @package Neomerx\Core
 */
class OrderStatus extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'order_statuses';

    /** Model field length */
    const NAME_MAX_LENGTH  = 50;
    /** Model field length */
    const CODE_MAX_LENGTH  = 50;

    /** Model field name */
    const FIELD_ID                 = 'id_order_status';
    /** Model field name */
    const FIELD_CODE               = 'code';
    /** Model field name */
    const FIELD_NAME               = 'name';
    /** Model field name */
    const FIELD_ORDERS             = 'orders';
    /** Model field name */
    const FIELD_AVAILABLE_STATUSES = 'availableStatuses';

    /** Order status code */
    const STATUS_NEW_ORDER = 'NEW_ORDER';

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
        self::FIELD_CODE,
        self::FIELD_NAME,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withAvailableStatuses()
    {
        return self::FIELD_AVAILABLE_STATUSES;
    }

    /**
     * @inheritdoc
     */
    protected function onDeleting()
    {
        // built-in statuses can't be removed
        return $this->code === self::STATUS_NEW_ORDER ? false : parent::onDeleting();
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, Order::FIELD_ID_ORDER_STATUS, self::FIELD_ID);
    }

    /**
     * Available statuses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availableStatuses()
    {
        return $this->belongsToMany(
            self::class,
            OrderStatusRule::TABLE_NAME,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO
        );

    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
