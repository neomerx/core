<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int          id_customer
 * @property      int          id_customer_risk
 * @property      int          id_customer_type
 * @property      int          id_language
 * @property      string       first_name
 * @property      string       last_name
 * @property      string       email
 * @property      string       mobile
 * @property      string       gender
 * @property-read Carbon       created_at
 * @property-read Carbon       updated_at
 * @property      CustomerRisk risk
 * @property      CustomerType type
 * @property      Language     language
 * @property      Collection   addresses
 * @property      Collection   billingAddresses
 * @property      Collection   shippingAddresses
 * @property      Collection   defaultShippingAddress An array of 0 or 1 items.
 * @property      Collection   defaultBillingAddress  An array of 0 or 1 items.
 * @property      Collection   customerAddresses
 * @property      Collection   orders
 *
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Customer extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'customers';

    /** Model field length */
    const FIRST_NAME_MAX_LENGTH = 50;
    /** Model field length */
    const LAST_NAME_MAX_LENGTH  = 50;
    /** Model field length */
    const EMAIL_MAX_LENGTH      = 100;
    /** Model field length */
    const MOBILE_MAX_LENGTH     = 13;

    /** Customer gender code */
    const GENDER_MALE   = 'male';
    /** Customer gender code */
    const GENDER_FEMALE = 'female';

    /** Model field name */
    const FIELD_ID                       = 'id_customer';
    /** Model field name */
    const FIELD_ID_CUSTOMER_RISK         = CustomerRisk::FIELD_ID;
    /** Model field name */
    const FIELD_ID_CUSTOMER_TYPE         = CustomerType::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE              = Language::FIELD_ID;
    /** Model field name */
    const FIELD_FIRST_NAME               = 'first_name';
    /** Model field name */
    const FIELD_LAST_NAME                = 'last_name';
    /** Model field name */
    const FIELD_EMAIL                    = 'email';
    /** Model field name */
    const FIELD_MOBILE                   = 'mobile';
    /** Model field name */
    const FIELD_GENDER                   = 'gender';
    /** Model field name */
    const FIELD_CREATED_AT               = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT               = 'updated_at';
    /** Model field name */
    const FIELD_RISK                     = 'risk';
    /** Model field name */
    const FIELD_TYPE                     = 'type';
    /** Model field name */
    const FIELD_LANGUAGE                 = 'language';
    /** Model field name */
    const FIELD_ADDRESSES                = 'addresses';
    /** Model field name */
    const FIELD_BILLING_ADDRESSES        = 'billingAddresses';
    /** Model field name */
    const FIELD_SHIPPING_ADDRESSES       = 'shippingAddresses';
    /** Model field name */
    const FIELD_DEFAULT_SHIPPING_ADDRESS = 'defaultShippingAddress';
    /** Model field name */
    const FIELD_DEFAULT_BILLING_ADDRESS  = 'defaultBillingAddress';
    /** Model field name */
    const FIELD_CUSTOMER_ADDRESSES       = 'customerAddresses';
    /** Model field name */
    const FIELD_ORDERS                   = 'orders';

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
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
        self::FIELD_EMAIL,
        self::FIELD_MOBILE,
        self::FIELD_GENDER,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_CUSTOMER_RISK,
        self::FIELD_ID_CUSTOMER_TYPE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CUSTOMER_RISK,
        self::FIELD_ID_CUSTOMER_TYPE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER_RISK => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerRisk::TABLE_NAME,

            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerType::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_FIRST_NAME  => 'required|alpha_dash|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME   => 'sometimes|required|alpha_dash|min:1|max:'.self::LAST_NAME_MAX_LENGTH,

            self::FIELD_EMAIL  => 'required|email|min:1|max:'.self::EMAIL_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_MOBILE => 'required|min:12|max:'.self::MOBILE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_GENDER => 'required|in:'.self::GENDER_MALE.','.self::GENDER_FEMALE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER_RISK => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerRisk::TABLE_NAME,

            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerType::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_FIRST_NAME  => 'sometimes|required|alpha_dash|min:1|max:'.self::FIRST_NAME_MAX_LENGTH,
            self::FIELD_LAST_NAME   => 'sometimes|required|alpha_dash|min:1|max:'.self::LAST_NAME_MAX_LENGTH,

            self::FIELD_EMAIL       => 'sometimes|required|email|min:1|max:'.
                self::EMAIL_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_GENDER => 'sometimes|required|in:'.self::GENDER_MALE.','.self::GENDER_FEMALE,
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withType()
    {
        return self::FIELD_TYPE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withRisk()
    {
        return self::FIELD_RISK;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withLanguage()
    {
        return self::FIELD_LANGUAGE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withDefaultBillingAddress()
    {
        return self::FIELD_DEFAULT_BILLING_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withDefaultShippingAddress()
    {
        return self::FIELD_DEFAULT_SHIPPING_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * Relation to customer risk.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function risk()
    {
        return $this->belongsTo(CustomerRisk::class, self::FIELD_ID_CUSTOMER_RISK, CustomerRisk::FIELD_ID);
    }

    /**
     * Relation to customer type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(CustomerType::class, self::FIELD_ID_CUSTOMER_TYPE, CustomerType::FIELD_ID);
    }

    /**
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }

    /**
     * Relation to addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addresses()
    {
        return $this->belongsToMany(
            Address::class,
            CustomerAddress::TABLE_NAME,
            CustomerAddress::FIELD_ID_CUSTOMER,
            CustomerAddress::FIELD_ID_ADDRESS
        )->withPivot([CustomerAddress::FIELD_TYPE, CustomerAddress::FIELD_IS_DEFAULT]);
    }

    /**
     * Relation to shipping addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shippingAddresses()
    {
        return $this->addresses()->wherePivot(CustomerAddress::FIELD_TYPE, '=', CustomerAddress::TYPE_SHIPPING);
    }

    /**
     * Relation to billing addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function billingAddresses()
    {
        return $this->addresses()->wherePivot(CustomerAddress::FIELD_TYPE, '=', CustomerAddress::TYPE_BILLING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function defaultShippingAddress()
    {
        return $this->shippingAddresses()
            ->wherePivot(CustomerAddress::FIELD_IS_DEFAULT, '=', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function defaultBillingAddress()
    {
        return $this->billingAddresses()
            ->wherePivot(CustomerAddress::FIELD_IS_DEFAULT, '=', true);
    }

    /**
     * Relation to customer addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class, CustomerAddress::FIELD_ID_CUSTOMER, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, Order::FIELD_ID_CUSTOMER, self::FIELD_ID);
    }
}
