<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_measurement
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection characteristics
 * @property      Collection properties
 * @method        Builder    withProperties()
 */
class Measurement extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'measurements';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID              = 'id_measurement';
    const FIELD_CODE            = 'code';
    const FIELD_CREATED_AT      = 'created_at';
    const FIELD_UPDATED_AT      = 'updated_at';
    const FIELD_CHARACTERISTICS = 'characteristics';
    const FIELD_PROPERTIES      = 'properties';

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
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:' . self::CODE_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithProperties(Builder $query)
    {
        return $query->with([self::FIELD_PROPERTIES.'.'.MeasurementProperties::FIELD_LANGUAGE]);
    }

    /**
     * Relation to characteristics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function characteristics()
    {
        return $this->hasMany(Characteristic::BIND_NAME, Characteristic::FIELD_ID_MEASUREMENT, self::FIELD_ID);
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
            MeasurementProperties::BIND_NAME,
            MeasurementProperties::FIELD_ID_MEASUREMENT,
            self::FIELD_ID
        );
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
