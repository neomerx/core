<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int       id_inventory
 * @property      int       id_product
 * @property      int       id_warehouse
 * @property      int       in
 * @property      int       out
 * @property      int       reserved_in
 * @property      int       reserved_out
 * @property-read int       available
 * @property-read int       reserved
 * @property-read int       quantity
 * @property-read Carbon    created_at
 * @property-read Carbon    updated_at
 * @property      Product   product
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
    const FIELD_ID_PRODUCT   = Product::FIELD_ID;
    /** Model field name */
    const FIELD_IN           = 'in';
    /** Model field name */
    const FIELD_OUT          = 'out';
    /** Model field name */
    const FIELD_RESERVED     = 'reserved';
    /** Model field name */
    const FIELD_RESERVED_IN  = 'reserved_in';
    /** Model field name */
    const FIELD_RESERVED_OUT = 'reserved_out';
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
    const FIELD_PRODUCT      = 'product';

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
        self::FIELD_RESERVED_IN,
        self::FIELD_RESERVED_OUT,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_WAREHOUSE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_WAREHOUSE,
    ];

    /**
     * @inheritdoc
     */
    protected $appends = [
        self::FIELD_QUANTITY,
        self::FIELD_RESERVED,
        self::FIELD_AVAILABLE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT   => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_IN           => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_OUT          => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_RESERVED_IN  => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_RESERVED_OUT => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_ID_WAREHOUSE => 'required|integer|min:1|max:4294967295',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT   => 'forbidden',
            self::FIELD_IN           => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_OUT          => 'sometimes|required|integer|min:0|max:18446744073709551615',
            self::FIELD_RESERVED_IN  => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_RESERVED_OUT => 'sometimes|required|integer|min:0|max:4294967295',
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
        $quantity = $this->getQuantityAttribute();
        $reserved = $this->getReservedAttribute();
        $result   = $quantity - $reserved;

        return $result;

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
     * Get quantity of items (incl. reserved).
     *
     * @return int
     */
    public function getReservedAttribute()
    {
        return (int)$this->attributes[self::FIELD_RESERVED_IN] - (int)$this->attributes[self::FIELD_RESERVED_OUT];
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
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
}
