<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property      int        id_address
 * @property      int        id_region
 * @property      string     city
 * @property      string     postcode
 * @property      string     address1
 * @property      string     address2
 * @property      Region     region
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property-read Carbon     deleted_at
 * @property      Collection billingCustomers
 * @property      Collection shippingCustomers
 * @property      Collection billingOrders
 * @property      Collection shippingOrders
 *
 * @package Neomerx\Core
 */
class Address extends BaseModel
{
    use SoftDeletes;

    /** Model table name */
    const TABLE_NAME = 'addresses';

    /** Model field length */
    const CITY_MAX_LENGTH      = 50;
    /** Model field length */
    const POSTCODE_MAX_LENGTH  = 12;
    /** Model field length */
    const ADDRESS_1_MAX_LENGTH = 100;
    /** Model field length */
    const ADDRESS_2_MAX_LENGTH = 100;

    /** Model field name */
    const FIELD_ID                 = 'id_address';
    /** Model field name */
    const FIELD_ID_REGION          = Region::FIELD_ID;
    /** Model field name */
    const FIELD_CITY               = 'city';
    /** Model field name */
    const FIELD_POSTCODE           = 'postcode';
    /** Model field name */
    const FIELD_ADDRESS1           = 'address1';
    /** Model field name */
    const FIELD_ADDRESS2           = 'address2';
    /** Model field name */
    const FIELD_DELETED_AT         = 'deleted_at';
    /** Model field name */
    const FIELD_REGION             = 'region';
    /** Model field name */
    const FIELD_BILLING_CUSTOMERS  = 'billingCustomers';
    /** Model field name */
    const FIELD_SHIPPING_CUSTOMERS = 'shippingCustomers';
    /** Model field name */
    const FIELD_BILLING_ORDERS     = 'billingOrders';
    /** Model field name */
    const FIELD_SHIPPING_ORDERS    = 'shippingOrders';
    /** Model field name */
    const FIELD_CREATED_AT         = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT         = 'updated_at';

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
        self::FIELD_CITY,
        self::FIELD_POSTCODE,
        self::FIELD_ADDRESS1,
        self::FIELD_ADDRESS2,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_REGION,
        self::FIELD_DELETED_AT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_REGION,
        self::FIELD_DELETED_AT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_REGION => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Region::TABLE_NAME,
            self::FIELD_CITY      => 'required|min:1|max:'.self::CITY_MAX_LENGTH,
            self::FIELD_POSTCODE  => 'sometimes|required|alpha_dash|min:1|max:'.self::POSTCODE_MAX_LENGTH,
            self::FIELD_ADDRESS1  => 'required|required|min:1|max:'.self::ADDRESS_1_MAX_LENGTH,
            self::FIELD_ADDRESS2  => 'sometimes|required|min:1|max:'.self::ADDRESS_2_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_REGION => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Region::TABLE_NAME,
            self::FIELD_CITY      => 'sometimes|required|min:1|max:'.self::CITY_MAX_LENGTH,
            self::FIELD_POSTCODE  => 'sometimes|required|alpha_dash|min:1|max:'.self::POSTCODE_MAX_LENGTH,
            self::FIELD_ADDRESS1  => 'sometimes|required|min:1|max:'.self::ADDRESS_1_MAX_LENGTH,
            self::FIELD_ADDRESS2  => 'sometimes|required|min:1|max:'.self::ADDRESS_2_MAX_LENGTH,
        ];
    }

    /**
     * Relation to region.
     *
     * @return string
     */
    public static function withRegion()
    {
        return self::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * Relation to region.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, self::FIELD_ID_REGION, Region::FIELD_ID);
    }

    /**
     * Relation to customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function billingCustomers()
    {
        return $this->belongsToMany(
            Customer::class,
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
            Customer::class,
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
        return $this->hasMany(Order::class, Order::FIELD_ID_BILLING_ADDRESS, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingOrders()
    {
        return $this->hasMany(Order::class, Order::FIELD_ID_SHIPPING_ADDRESS, self::FIELD_ID);
    }
}
