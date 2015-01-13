<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_shipping_order_status
 * @property string     name
 * @property string     code
 * @property Collection shipping_orders
 */
class ShippingOrderStatus extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'shipping_order_statuses';

    const NAME_MAX_LENGTH = 50;
    const CODE_MAX_LENGTH = 50;

    const FIELD_ID              = 'id_shipping_order_status';
    const FIELD_CODE            = 'code';
    const FIELD_NAME            = 'name';
    const FIELD_SHIPPING_ORDERS = 'shipping_orders';

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
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * Relation to shipping orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingOrders()
    {
        return $this->hasMany(ShippingOrder::BIND_NAME, ShippingOrder::FIELD_ID_SHIPPING_ORDER_STATUS, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
