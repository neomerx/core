<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_carrier
 * @property      string     code
 * @property      float      min_weight
 * @property      float      max_weight
 * @property      int        min_cost
 * @property      int        max_cost
 * @property      float      min_dimension
 * @property      float      max_dimension
 * @property      bool       is_taxable
 * @property      string     settings
 * @property      string     data
 * @property      string     cache
 * @property      string     factory
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Currency   currency
 * @property      Collection orders
 * @property      Collection properties
 * @property      Collection customerTypes
 * @property      Collection postcodes
 * @property      Collection territories
 * @property      Collection shippingOrders
 *
 * @package Neomerx\Core
 */
class Carrier extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'carriers';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID              = 'id_carrier';
    /** Model field name */
    const FIELD_CODE            = 'code';
    /** Model field name */
    const FIELD_MIN_WEIGHT      = 'min_weight';
    /** Model field name */
    const FIELD_MAX_WEIGHT      = 'max_weight';
    /** Model field name */
    const FIELD_MIN_COST        = 'min_cost';
    /** Model field name */
    const FIELD_MAX_COST        = 'max_cost';
    /** Model field name */
    const FIELD_ID_CURRENCY     = Currency::FIELD_ID;
    /** Model field name */
    const FIELD_MIN_DIMENSION   = 'min_dimension';
    /** Model field name */
    const FIELD_MAX_DIMENSION   = 'max_dimension';
    /** Model field name */
    const FIELD_IS_TAXABLE      = 'is_taxable';
    /** Model field name */
    const FIELD_SETTINGS        = 'settings';
    /** Model field name */
    const FIELD_DATA            = 'data';
    /** Model field name */
    const FIELD_CACHE           = 'cache';
    /** Model field name */
    const FIELD_CALCULATOR_CODE = 'calculator_code';
    /** Model field name */
    const FIELD_CREATED_AT      = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT      = 'updated_at';
    /** Model field name */
    const FIELD_ORDERS          = 'orders';
    /** Model field name */
    const FIELD_PROPERTIES      = 'properties';
    /** Model field name */
    const FIELD_TERRITORIES     = 'territories';
    /** Model field name */
    const FIELD_POSTCODES       = 'postcodes';
    /** Model field name */
    const FIELD_CUSTOMER_TYPES  = 'customerTypes';
    /** Model field name */
    const FIELD_SHIPPING_ORDERS = 'shippingOrders';
    /** Model field name */
    const FIELD_CURRENCY        = 'currency';

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
        self::FIELD_CODE,
        self::FIELD_MIN_WEIGHT,
        self::FIELD_MAX_WEIGHT,
        self::FIELD_MIN_COST,
        self::FIELD_MAX_COST,
        self::FIELD_MIN_DIMENSION,
        self::FIELD_MAX_DIMENSION,
        self::FIELD_IS_TAXABLE,
        self::FIELD_SETTINGS,
        self::FIELD_DATA,
        self::FIELD_CALCULATOR_CODE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_CACHE,
        self::FIELD_ID_CURRENCY,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_CACHE,
        self::FIELD_ID_CURRENCY,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_MIN_WEIGHT      => 'sometimes|required|numeric|min:0',
            self::FIELD_MAX_WEIGHT      => 'sometimes|required|numeric|min:0',
            self::FIELD_MIN_COST        => 'sometimes|required|integer|min:0',
            self::FIELD_MAX_COST        => 'sometimes|required|integer|min:0',
            self::FIELD_ID_CURRENCY     => 'required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,
            self::FIELD_MIN_DIMENSION   => 'sometimes|required|numeric|min:0',
            self::FIELD_MAX_DIMENSION   => 'sometimes|required|numeric|min:0',
            self::FIELD_IS_TAXABLE      => 'required|boolean',
            self::FIELD_SETTINGS        => 'sometimes|required',
            self::FIELD_DATA            => 'sometimes|required',
            self::FIELD_CACHE           => 'sometimes|required|forbidden',
            self::FIELD_CALCULATOR_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE        => 'sometimes|required|forbidden',
            self::FIELD_MIN_WEIGHT  => 'sometimes|required|numeric|min:0',
            self::FIELD_MAX_WEIGHT  => 'sometimes|required|numeric|min:0',
            self::FIELD_MIN_COST    => 'sometimes|required|integer|min:0',
            self::FIELD_MAX_COST    => 'sometimes|required|integer|min:0',

            self::FIELD_ID_CURRENCY => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,

            self::FIELD_MIN_DIMENSION   => 'sometimes|required|numeric|min:0',
            self::FIELD_MAX_DIMENSION   => 'sometimes|required|numeric|min:0',
            self::FIELD_IS_TAXABLE      => 'sometimes|required|boolean',
            self::FIELD_SETTINGS        => 'sometimes|required',
            self::FIELD_DATA            => 'sometimes|required',
            self::FIELD_CACHE           => 'sometimes|required|forbidden',
            self::FIELD_CALCULATOR_CODE => 'sometimes|required|code|min:1|max:'.self::CODE_MAX_LENGTH,
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CarrierProperty::FIELD_LANGUAGE;
    }

    /**
     * @param bool $value
     */
    public function setIsTaxableAttribute($value)
    {
        $this->attributes[self::FIELD_IS_TAXABLE] = (bool)$value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsTaxableAttribute($value)
    {
        return (bool)$value;
    }

    /**
     * @return string
     */
    public static function withCurrency()
    {
        return self::FIELD_CURRENCY;
    }

    /**
     * Relation to shipping orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingOrders()
    {
        return $this->hasMany(ShippingOrder::class, ShippingOrder::FIELD_ID_CARRIER, self::FIELD_ID);
    }

    /**
     * Relation to language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(CarrierProperty::class, CarrierProperty::FIELD_ID_CARRIER, self::FIELD_ID);
    }

    /**
     * Relation to rule customer types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerTypes()
    {
        return $this->hasMany(CarrierCustomerType::class, CarrierCustomerType::FIELD_ID_CARRIER, self::FIELD_ID);
    }

    /**
     * Relation to rule postcodes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postcodes()
    {
        return $this->hasMany(CarrierPostcode::class, CarrierPostcode::FIELD_ID_CARRIER, self::FIELD_ID);
    }

    /**
     * Relation to rule territories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function territories()
    {
        return $this->hasMany(CarrierTerritory::class, CarrierTerritory::FIELD_ID_CARRIER, self::FIELD_ID);
    }

    /**
     * Relation to currency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, self::FIELD_ID_CURRENCY, Currency::FIELD_ID);
    }
}
