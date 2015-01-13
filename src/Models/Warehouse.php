<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Builder;

/**
 * @property int       id_warehouse
 * @property int       id_address
 * @property int       id_store
 * @property string    code
 * @property string    name
 * @property Address   address
 * @property Store     store
 * @property Inventory inventory
 * @method   Builder   withAddress()
 * @method   Builder   withStore()
 * @method   Builder   withInventory()
 */
class Warehouse extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'warehouses';

    const CODE_MAX_LENGTH = 50;
    const NAME_MAX_LENGTH = 50;

    const FIELD_ID         = 'id_warehouse';
    const FIELD_ID_ADDRESS = Address::FIELD_ID;
    const FIELD_ID_STORE   = Store::FIELD_ID;
    const FIELD_CODE       = 'code';
    const FIELD_NAME       = 'name';
    const FIELD_ADDRESS    = 'address';
    const FIELD_STORE      = 'store';
    const FIELD_INVENTORY  = 'inventory';

    const DEFAULT_ID   = 1;
    const DEFAULT_CODE = 'DEFAULT';

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
        self::FIELD_ID_STORE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ADDRESS,
        self::FIELD_ID_STORE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH. '|unique:'.self::TABLE_NAME,

            self::FIELD_NAME       => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ID_ADDRESS => 'required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
            self::FIELD_ID_STORE   => 'required|integer|min:1|max:4294967295|exists:'.Store::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithAddress(Builder $query)
    {
        return $query->with([self::FIELD_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY]);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithStore(Builder $query)
    {
        return $query->with([self::FIELD_STORE]);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithInventory(Builder $query)
    {
        return $query->with([self::FIELD_INVENTORY]);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::BIND_NAME, self::FIELD_ID_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::BIND_NAME, self::FIELD_ID_STORE, Store::FIELD_ID);
    }

    /**
     * Relation to inventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::BIND_NAME, Inventory::FIELD_ID_WAREHOUSE, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
