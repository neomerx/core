<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_supply_order
 * @property      int        id_supplier
 * @property      int        id_warehouse
 * @property      int        id_currency
 * @property      int        id_language
 * @property      string     status
 * @property      Carbon     expected_at
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection details
 * @property      Supplier   supplier
 * @property      Warehouse  warehouse
 * @property      Currency   currency
 * @property      Language   language
 *
 * @package Neomerx\Core
 */
class SupplyOrder extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'supply_orders';

    /** Supply order status code */
    const STATUS_DRAFT     = 'draft';
    /** Supply order status code */
    const STATUS_VALIDATED = 'validated';
    /** Supply order status code */
    const STATUS_CANCELLED = 'cancelled';

    /** Model field name */
    const FIELD_ID           = 'id_supply_order';
    /** Model field name */
    const FIELD_ID_SUPPLIER  = Supplier::FIELD_ID;
    /** Model field name */
    const FIELD_ID_WAREHOUSE = Warehouse::FIELD_ID;
    /** Model field name */
    const FIELD_ID_CURRENCY  = Currency::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE  = Language::FIELD_ID;
    /** Model field name */
    const FIELD_STATUS       = 'status';
    /** Model field name */
    const FIELD_EXPECTED_AT  = 'expected_at';
    /** Model field name */
    const FIELD_CREATED_AT   = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT   = 'updated_at';
    /** Model field name */
    const FIELD_DETAILS      = 'details';
    /** Model field name */
    const FIELD_SUPPLIER     = 'supplier';
    /** Model field name */
    const FIELD_WAREHOUSE    = 'warehouse';
    /** Model field name */
    const FIELD_CURRENCY     = 'currency';
    /** Model field name */
    const FIELD_LANGUAGE     = 'language';

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
        self::FIELD_EXPECTED_AT,
        self::FIELD_STATUS,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_SUPPLIER,
        self::FIELD_ID_WAREHOUSE,
        self::FIELD_ID_CURRENCY,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_SUPPLIER,
        self::FIELD_ID_WAREHOUSE,
        self::FIELD_ID_CURRENCY,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $dates = [
        self::FIELD_EXPECTED_AT
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_SUPPLIER  => 'required|integer|min:1|max:4294967295|exists:'.Supplier::TABLE_NAME,
            self::FIELD_ID_WAREHOUSE => 'required|integer|min:1|max:4294967295|exists:'.Warehouse::TABLE_NAME,
            self::FIELD_ID_CURRENCY  => 'required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,
            self::FIELD_ID_LANGUAGE  => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_EXPECTED_AT  => 'required|date|after:now',

            self::FIELD_STATUS => 'required|in:'.
                self::STATUS_DRAFT.','.self::STATUS_VALIDATED.','.self::STATUS_CANCELLED,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_SUPPLIER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Supplier::TABLE_NAME,

            self::FIELD_ID_WAREHOUSE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Warehouse::TABLE_NAME,

            self::FIELD_ID_CURRENCY => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_EXPECTED_AT => 'sometimes|required|date|after:now',

            self::FIELD_STATUS => 'sometimes|required|in:'.
                self::STATUS_DRAFT.','.self::STATUS_VALIDATED.','.self::STATUS_CANCELLED,
        ];
    }

    /**
     * @return string
     */
    public static function withSupplier()
    {
        return self::FIELD_SUPPLIER;
    }

    /**
     * @return string
     */
    public static function withWarehouse()
    {
        return self::FIELD_WAREHOUSE;
    }

    /**
     * @return string
     */
    public static function withCurrency()
    {
        return self::FIELD_CURRENCY;
    }

    /**
     * @return string
     */
    public static function withLanguage()
    {
        return self::FIELD_LANGUAGE;
    }

    /**
     * Relation to supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, self::FIELD_ID_SUPPLIER, Supplier::FIELD_ID);
    }

    /**
     * Relation to warehouse.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, self::FIELD_ID_WAREHOUSE, Warehouse::FIELD_ID);
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
     * Relation to supply order details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(SupplyOrderDetails::class, SupplyOrderDetails::FIELD_ID_SUPPLY_ORDER, self::FIELD_ID);
    }
}
