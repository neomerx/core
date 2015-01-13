<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_supplier
 * @property      int        id_address
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Address    address
 * @property      Collection properties
 * @method        Builder    withAddress()
 * @method        Builder    withProperties()
 */
class Supplier extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'suppliers';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID         = 'id_supplier';
    const FIELD_ID_ADDRESS = 'id_address';
    const FIELD_CODE       = 'code';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';
    const FIELD_ADDRESS    = 'address';
    const FIELD_PROPERTIES = 'properties';

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
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ADDRESS,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH .
                '|unique:'.self::TABLE_NAME,

            self::FIELD_ID_ADDRESS => 'required|integer|min:1|max:4294967295'.'|exists:'.Address::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE       => 'sometimes|required|forbidden',
            self::FIELD_ID_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
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
    public function scopeWithProperties(Builder $query)
    {
        return $query->with([self::FIELD_PROPERTIES.'.'.SupplierProperties::FIELD_LANGUAGE]);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::BIND_NAME, self::FIELD_ID_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to supplier language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(SupplierProperties::BIND_NAME, SupplierProperties::FIELD_ID_SUPPLIER, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
