<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int         id_characteristic
 * @property      int         id_measurement
 * @property      string      code
 * @property-read Carbon      created_at
 * @property-read Carbon      updated_at
 * @property      Measurement measurement
 * @property      array       specification
 * @property      array       values
 * @property      Collection  properties
 * @method        Builder     withProperties()
 * @method        Builder     withMeasurement()
 */
class Characteristic extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'characteristic';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID             = 'id_characteristic';
    const FIELD_ID_MEASUREMENT = Measurement::FIELD_ID;
    const FIELD_CODE           = 'code';
    const FIELD_CREATED_AT     = 'created_at';
    const FIELD_UPDATED_AT     = 'updated_at';
    const FIELD_MEASUREMENT    = 'measurement';
    const FIELD_SPECIFICATION  = 'specification';
    const FIELD_VALUES         = 'values';
    const FIELD_PROPERTIES     = 'properties';

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
        self::FIELD_ID_MEASUREMENT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_MEASUREMENT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_ID_MEASUREMENT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Measurement::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE           => 'sometimes|required|forbidden',
            self::FIELD_ID_MEASUREMENT => 'sometimes|integer|min:1|max:4294967295|exists:'.Measurement::TABLE_NAME,
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithProperties(Builder $query)
    {
        return $query->with([self::FIELD_PROPERTIES.'.'.CharacteristicProperties::FIELD_LANGUAGE]);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithMeasurement(Builder $query)
    {
        return $query->with([
            self::FIELD_MEASUREMENT.'.'.Measurement::FIELD_PROPERTIES.'.'.MeasurementProperties::FIELD_LANGUAGE
        ]);
    }

    /**
     * Relation to language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(
            CharacteristicProperties::BIND_NAME,
            CharacteristicProperties::FIELD_ID_CHARACTERISTIC,
            self::FIELD_ID
        );
    }

    /**
     * Relation to values.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(
            CharacteristicValue::BIND_NAME,
            CharacteristicValue::FIELD_ID_CHARACTERISTIC,
            self::FIELD_ID
        );
    }

    /**
     * Relation to measurement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measurement()
    {
        return $this->belongsTo(Measurement::BIND_NAME, self::FIELD_ID_MEASUREMENT, Measurement::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
