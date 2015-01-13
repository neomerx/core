<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int            id_characteristic_value
 * @property      int            id_characteristic
 * @property      string         code
 * @property-read Carbon         created_at
 * @property-read Carbon         updated_at
 * @property      Characteristic characteristic
 * @property      Collection     properties
 * @property      Collection     specification
 * @method        Builder        withProperties()
 */
class CharacteristicValue extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'characteristic_values';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID                = 'id_characteristic_value';
    const FIELD_ID_CHARACTERISTIC = Characteristic::FIELD_ID;
    const FIELD_CODE              = 'code';
    const FIELD_CREATED_AT        = 'created_at';
    const FIELD_UPDATED_AT        = 'updated_at';
    const FIELD_CHARACTERISTIC    = 'characteristic';
    const FIELD_PROPERTIES        = 'properties';
    const FIELD_SPECIFICATION     = 'specification';

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
        self::FIELD_ID_CHARACTERISTIC,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CHARACTERISTIC,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH .
                '|unique:'.self::TABLE_NAME,

            self::FIELD_ID_CHARACTERISTIC => 'required|integer|min:1|max:4294967295|exists:' .
                Characteristic::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE              => 'sometimes|required|forbidden',
            self::FIELD_ID_CHARACTERISTIC => 'required|integer|min:1|max:4294967295|exists:' .
                Characteristic::TABLE_NAME,
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithProperties(Builder $query)
    {
        return $query->with([self::FIELD_PROPERTIES.'.'.CharacteristicValueProperties::FIELD_LANGUAGE]);
    }

    /**
     * Relation to characteristic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function characteristic()
    {
        return $this->belongsTo(Characteristic::BIND_NAME, self::FIELD_ID_CHARACTERISTIC, Characteristic::FIELD_ID);
    }

    /**
     * Relation to language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(
            CharacteristicValueProperties::BIND_NAME,
            CharacteristicValueProperties::FIELD_ID_CHARACTERISTIC_VALUE,
            self::FIELD_ID
        );
    }

    /**
     * Relation to product specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification()
    {
        return $this->hasMany(Specification::BIND_NAME, Specification::FIELD_ID_CHARACTERISTIC_VALUE, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }

    /**
     * @param array $codes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCodes(array $codes)
    {
        $builder = $this->newQuery();
        $builder->getQuery()->whereIn(self::FIELD_CODE, $codes);
        return $builder;
    }
}
