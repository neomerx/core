<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int       id_inventory
 * @property      int       id_variant
 * @property      int       id_warehouse
 * @property      int       in
 * @property      int       out
 * @property      int       reserved
 * @property-read int       available
 * @property-read int       quantity
 * @property-read Carbon    created_at
 * @property-read Carbon    updated_at
 * @property      Variant   variant
 * @property      Warehouse warehouse
 *
 * @package Neomerx\Core
 */
class Inventory extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'inventory';

    /** Model field name */
    const FIELD_ID           = 'id_inventory';
    /** Model field name */
    const FIELD_ID_WAREHOUSE = Warehouse::FIELD_ID;
    /** Model field name */
    const FIELD_ID_VARIANT   = Variant::FIELD_ID;
    /** Model field name */
    const FIELD_IN           = 'in';
    /** Model field name */
    const FIELD_OUT          = 'out';
    /** Model field name */
    const FIELD_RESERVED     = 'reserved';
    /** Model field name */
    const FIELD_AVAILABLE    = 'available';
    /** Model field name */
    const FIELD_QUANTITY     = 'quantity';
    /** Model field name */
    const FIELD_CREATED_AT   = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT   = 'updated_at';
    /** Model field name */
    const FIELD_WAREHOUSE    = 'warehouse';
    /** Model field name */
    const FIELD_VARIANT      = 'variant';

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
        self::FIELD_IN,
        self::FIELD_OUT,
        self::FIELD_RESERVED,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_WAREHOUSE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_WAREHOUSE,
    ];

    /**
     * @inheritdoc
     */
    protected $appends = [
        self::FIELD_QUANTITY,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_VARIANT   => 'required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_IN           => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_OUT          => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_RESERVED     => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_ID_WAREHOUSE => 'required|integer|min:1|max:4294967295',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_VARIANT   => 'forbidden',
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
        return (int)$this->attributes[self::FIELD_IN] - (int)$this->attributes[self::FIELD_OUT] -
        (int)$this->attributes[self::FIELD_RESERVED];
    }

    /**
     * Get quantity of items (incl. reserved).
     *
     * @return int
     */
    public function getQuantityAttribute()
    {
        return (int)$this->attributes[self::FIELD_IN] - (int)$this->attributes[self::FIELD_OUT];
    }

    /**
     * Relation to variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
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
     * @param int $variantId
     * @param int $warehouseId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByVariantAndWarehouse($variantId, $warehouseId)
    {
        return $this->newQuery()
            ->where(self::FIELD_ID_VARIANT, $variantId)
            ->where(self::FIELD_ID_WAREHOUSE, $warehouseId);
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
