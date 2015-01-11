<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int       id_inventory
 * @property      int       id_warehouse
 * @property      string    sku
 * @property      int       in
 * @property      int       out
 * @property      int       reserved
 * @property-read int       available
 * @property-read int       quantity
 * @property-read Carbon    created_at
 * @property-read Carbon    updated_at
 * @property      Warehouse warehouse
 */
class Inventory extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'inventory';

    const FIELD_ID           = 'id_inventory';
    const FIELD_ID_WAREHOUSE = Warehouse::FIELD_ID;
    const FIELD_SKU          = Variant::FIELD_SKU;
    const FIELD_IN           = 'in';
    const FIELD_OUT          = 'out';
    const FIELD_RESERVED     = 'reserved';
    const FIELD_AVAILABLE    = 'available';
    const FIELD_QUANTITY     = 'quantity';
    const FIELD_CREATED_AT   = 'created_at';
    const FIELD_UPDATED_AT   = 'updated_at';
    const FIELD_WAREHOUSE    = 'warehouse';

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
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_ID_WAREHOUSE,
        self::FIELD_SKU,
        self::FIELD_IN,
        self::FIELD_OUT,
        self::FIELD_RESERVED,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_WAREHOUSE,
    ];

    protected $appends = [
        self::FIELD_QUANTITY,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_SKU => 'required|alpha_dash|min:1|max:' . Product::SKU_MAX_LENGTH .
                '|exists:' . Variant::TABLE_NAME .','.Variant::FIELD_SKU,

            self::FIELD_IN           => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_OUT          => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_RESERVED     => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_ID_WAREHOUSE => 'required|integer|min:1|max:4294967295',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_SKU          => 'forbidden',
            self::FIELD_IN           => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_OUT          => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_RESERVED     => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_ID_WAREHOUSE => 'forbidden',
        ];
    }

    /**
     * Get quantity of available items (excl. reserved).
     *
     * @return int
     */
    public function getAvailableAttribute()
    {
        return (int)$this->attributes[self::FIELD_IN] - $this->attributes[self::FIELD_OUT] -
            $this->attributes[self::FIELD_RESERVED];
    }

    /**
     * Get quantity of items (incl. reserved).
     *
     * @return int
     */
    public function getQuantityAttribute()
    {
        return (int)$this->attributes[self::FIELD_IN] - $this->attributes[self::FIELD_OUT];
    }

    /**
     * Relation to warehouse.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::BIND_NAME, self::FIELD_ID_WAREHOUSE, Warehouse::FIELD_ID);
    }

    /**
     * @param string $sku
     * @param int    $warehouseId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectBySkuAndWarehouse($sku, $warehouseId)
    {
        return $this->newQuery()->where(self::FIELD_SKU, $sku)->where(self::FIELD_ID_WAREHOUSE, $warehouseId);
    }

    /**
     * Increment 'in' value.
     *
     * @param int $quantity
     *
     * @return int
     */
    public function incrementIn($quantity)
    {
        return $this->increment(self::FIELD_IN, $quantity);
    }

    /**
     * Increment 'out' value.
     *
     * @param int $quantity
     *
     * @return int
     */
    public function incrementOut($quantity)
    {
        return $this->increment(self::FIELD_OUT, $quantity);
    }

    /**
     * Increment 'reserved' value.
     *
     * @param int $quantity
     *
     * @return int
     */
    public function incrementReserved($quantity)
    {
        return $this->increment(self::FIELD_RESERVED, $quantity);
    }

    /**
     * Decrement 'reserved' value.
     *
     * @param int $quantity
     *
     * @return int
     */
    public function decrementReserved($quantity)
    {
        return $this->decrement(self::FIELD_RESERVED, $quantity);
    }
}
