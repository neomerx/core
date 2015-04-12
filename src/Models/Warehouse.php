<?php namespace Neomerx\Core\Models;

/**
 * @property int       id_warehouse
 * @property int       id_address
 * @property int       id_store
 * @property string    code
 * @property string    name
 * @property Address   address
 * @property Store     store
 * @property Inventory inventory
 *
 * @package Neomerx\Core
 */
class Warehouse extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'warehouses';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;
    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_warehouse';
    /** Model field name */
    const FIELD_ID_ADDRESS = Address::FIELD_ID;
    /** Model field name */
    const FIELD_ID_STORE   = Store::FIELD_ID;
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_NAME       = 'name';
    /** Model field name */
    const FIELD_ADDRESS    = 'address';
    /** Model field name */
    const FIELD_STORE      = 'store';
    /** Model field name */
    const FIELD_INVENTORY  = 'inventory';

    /** Default warehouse Id */
    const DEFAULT_ID   = 1;
    /** Default warehouse code */
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
        self::FIELD_ID_STORE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ADDRESS,
        self::FIELD_ID_STORE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_NAME       => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ID_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
            self::FIELD_ID_STORE   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Store::TABLE_NAME,
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
            self::FIELD_ID_STORE   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Store::TABLE_NAME,
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
    public static function withStore()
    {
        return self::FIELD_STORE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withInventory()
    {
        return self::FIELD_INVENTORY;
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
     * Relation to store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class, self::FIELD_ID_STORE, Store::FIELD_ID);
    }

    /**
     * Relation to inventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, Inventory::FIELD_ID_WAREHOUSE, self::FIELD_ID);
    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
