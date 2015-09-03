<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_store
 * @property int        id_address
 * @property string     code
 * @property string     name
 * @property Address    address
 * @property Collection orders
 * @property Collection warehouses
 *
 * @package Neomerx\Core
 */
class Store extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'stores';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;
    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_store';
    /** Model field name */
    const FIELD_ID_ADDRESS = Address::FIELD_ID;
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_NAME       = 'name';
    /** Model field name */
    const FIELD_ADDRESS    = 'address';
    /** Model field name */
    const FIELD_ORDERS     = 'orders';
    /** Model field name */
    const FIELD_WAREHOUSES = 'warehouses';

    /** Default store Id */
    const DEFAULT_ID   = 1;
    /** Default store code */
    const DEFAULT_CODE = 'DEFAULT';

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
        self::FIELD_ID_ADDRESS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_NAME       => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ID_ADDRESS => 'required|integer|min:1|max:4294967295'.'|exists:'.Address::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE       => 'sometimes|required|forbidden',
            self::FIELD_NAME       => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ID_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withAddress()
    {
        return self::FIELD_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withWarehouses()
    {
        return self::FIELD_WAREHOUSES;
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class, self::FIELD_ID_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to warehouses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warehouses()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(Warehouse::class, Warehouse::FIELD_ID_STORE, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(Order::class, Order::FIELD_ID_STORE, self::FIELD_ID);
    }
}
