<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * @property int        id_address
 * @property int        id_region
 * @property string     city
 * @property string     postcode
 * @property string     address1
 * @property string     address2
 * @property Carbon     deleted_at
 * @property Region     region
 * @property Collection billing_customers
 * @property Collection shipping_customers
 * @property Collection billing_orders
 * @property Collection shipping_orders
 * @method   Builder    withRegionAndCountry()
 */
class Address extends BaseModel
{
    use SoftDeletingTrait;

    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'addresses';

    const CITY_MAX_LENGTH      = 50;
    const POSTCODE_MAX_LENGTH  = 12;
    const ADDRESS_1_MAX_LENGTH = 100;
    const ADDRESS_2_MAX_LENGTH = 100;

    const FIELD_ID                 = 'id_address';
    const FIELD_ID_REGION          = Region::FIELD_ID;
    const FIELD_CITY               = 'city';
    const FIELD_POSTCODE           = 'postcode';
    const FIELD_ADDRESS1           = 'address1';
    const FIELD_ADDRESS2           = 'address2';
    const FIELD_DELETED_AT         = 'deleted_at';
    const FIELD_REGION             = 'region';
    const FIELD_BILLING_CUSTOMERS  = 'billing_customers';
    const FIELD_SHIPPING_CUSTOMERS = 'shipping_customers';
    const FIELD_BILLING_ORDERS     = 'billing_orders';
    const FIELD_SHIPPING_ORDERS    = 'shipping_orders';

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
        self::FIELD_ID_REGION,
        self::FIELD_CITY,
        self::FIELD_POSTCODE,
        self::FIELD_ADDRESS1,
        self::FIELD_ADDRESS2,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_REGION,
        self::FIELD_DELETED_AT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_REGION => 'required|integer|min:1|max:4294967295|exists:' . Region::TABLE_NAME,
            self::FIELD_CITY      => 'required|min:1|max:'                           . self::CITY_MAX_LENGTH,
            self::FIELD_POSTCODE  => 'sometimes|required|alpha_dash|min:1|max:'      . self::POSTCODE_MAX_LENGTH,
            self::FIELD_ADDRESS1  => 'sometimes|required|required|min:1|max:'        . self::ADDRESS_1_MAX_LENGTH,
            self::FIELD_ADDRESS2  => 'sometimes|required|min:1|max:'                 . self::ADDRESS_2_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_REGION => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Region::TABLE_NAME,
            self::FIELD_CITY      => 'sometimes|required|min:1|max:'                           . self::CITY_MAX_LENGTH,
            self::FIELD_POSTCODE  => 'sometimes|required|alpha_dash|min:1|max:' . self::POSTCODE_MAX_LENGTH,
            self::FIELD_ADDRESS1  => 'sometimes|required|min:1|max:'            . self::ADDRESS_1_MAX_LENGTH,
            self::FIELD_ADDRESS2  => 'sometimes|required|min:1|max:'            . self::ADDRESS_2_MAX_LENGTH,
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithRegionAndCountry(Builder $query)
    {
        return $query->with([self::FIELD_REGION.'.'.Region::FIELD_COUNTRY]);
    }

    /**
     * Relation to region.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::BIND_NAME, self::FIELD_ID_REGION, Region::FIELD_ID);
    }

    /**
     * Relation to customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function billingCustomers()
    {
        return $this->belongsToMany(
            Customer::BIND_NAME,
            CustomerAddress::TABLE_NAME,
            CustomerAddress::FIELD_ID_ADDRESS,
            CustomerAddress::FIELD_ID_CUSTOMER
        )->wherePivot(CustomerAddress::FIELD_TYPE, '=', CustomerAddress::TYPE_BILLING);
    }

    /**
     * Relation to customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shippingCustomers()
    {
        return $this->belongsToMany(
            Customer::BIND_NAME,
            CustomerAddress::TABLE_NAME,
            CustomerAddress::FIELD_ID_ADDRESS,
            CustomerAddress::FIELD_ID_CUSTOMER
        )->wherePivot(CustomerAddress::FIELD_TYPE, '=', CustomerAddress::TYPE_SHIPPING);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billingOrders()
    {
        return $this->hasMany(Order::BIND_NAME, Order::FIELD_ID_BILLING_ADDRESS, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingOrders()
    {
        return $this->hasMany(Order::BIND_NAME, Order::FIELD_ID_SHIPPING_ADDRESS, self::FIELD_ID);
    }
}
