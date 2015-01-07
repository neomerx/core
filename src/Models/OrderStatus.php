<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_order_status
 * @property string     name
 * @property string     code
 * @property Collection orders
 * @property Collection available_statuses
 * @method   Builder    withAvailableStatuses()
 */
class OrderStatus extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'order_statuses';

    const NAME_MAX_LENGTH  = 50;
    const CODE_MAX_LENGTH  = 50;

    const FIELD_ID                 = 'id_order_status';
    const FIELD_CODE               = 'code';
    const FIELD_NAME               = 'name';
    const FIELD_ORDERS             = 'orders';
    const FIELD_AVAILABLE_STATUSES = 'available_statuses';

    const STATUS_NEW_ORDER = 'NEW_ORDER';

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
        self::FIELD_CODE,
        self::FIELD_NAME,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public static function getInputOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH,
            self::FIELD_NAME => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,

            self::FIELD_NAME => 'required|min:1|max:' . self::NAME_MAX_LENGTH . '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:' . self::NAME_MAX_LENGTH . '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithAvailableStatuses(Builder $query)
    {
        return $query->with([
            camel_case(self::FIELD_AVAILABLE_STATUSES),
        ]);
    }

    /**
     * {@inheritdoc}
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
        return $this->hasMany(Order::BIND_NAME, Order::FIELD_ID_ORDER_STATUS, self::FIELD_ID);
    }

    /**
     * Available statuses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availableStatuses()
    {
        return $this->belongsToMany(
            OrderStatus::BIND_NAME,
            OrderStatusRule::TABLE_NAME,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_FROM,
            OrderStatusRule::FIELD_ID_ORDER_STATUS_TO
        );

    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
