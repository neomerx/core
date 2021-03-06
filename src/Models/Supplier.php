<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_supplier
 * @property      int        id_address
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Address    address
 * @property      Collection properties
 *
 * @package Neomerx\Core
 */
class Supplier extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'suppliers';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_supplier';
    /** Model field name */
    const FIELD_ID_ADDRESS = 'id_address';
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';
    /** Model field name */
    const FIELD_ADDRESS    = 'address';
    /** Model field name */
    const FIELD_PROPERTIES = 'properties';

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
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ID_ADDRESS => 'required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ID_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withAddress()
    {
        return self::FIELD_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.ManufacturerProperty::FIELD_LANGUAGE;
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class, self::FIELD_ID_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to supplier language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(SupplierProperty::class, SupplierProperty::FIELD_ID_SUPPLIER, self::FIELD_ID);
    }
}
