<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int         id_feature
 * @property      int         id_measurement
 * @property      string      code
 * @property-read Carbon      created_at
 * @property-read Carbon      updated_at
 * @property      Measurement measurement
 * @property      Collection  values
 * @property      Collection  properties
 *
 * @package Neomerx\Core
 */
class Feature extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'features';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID             = 'id_feature';
    /** Model field name */
    const FIELD_ID_MEASUREMENT = Measurement::FIELD_ID;
    /** Model field name */
    const FIELD_CODE           = 'code';
    /** Model field name */
    const FIELD_CREATED_AT     = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT     = 'updated_at';
    /** Model field name */
    const FIELD_MEASUREMENT    = 'measurement';
    /** Model field name */
    const FIELD_VALUES         = 'values';
    /** Model field name */
    const FIELD_PROPERTIES     = 'properties';

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
        self::FIELD_ID_MEASUREMENT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_MEASUREMENT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE           => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_ID_MEASUREMENT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Measurement::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE           => 'sometimes|required|forbidden',
            self::FIELD_ID_MEASUREMENT => 'sometimes|integer|min:1|max:4294967295|exists:'.Measurement::TABLE_NAME,
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.FeatureProperties::FIELD_LANGUAGE;
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withMeasurement()
    {
        return self::FIELD_MEASUREMENT.'.'.Measurement::FIELD_PROPERTIES.'.'.MeasurementProperties::FIELD_LANGUAGE;
    }

    /**
     * Relation to language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(FeatureProperties::class, FeatureProperties::FIELD_ID_FEATURE, self::FIELD_ID);
    }

    /**
     * Relation to values.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(FeatureValue::class, FeatureValue::FIELD_ID_FEATURE, self::FIELD_ID);
    }

    /**
     * Relation to measurement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measurement()
    {
        return $this->belongsTo(Measurement::class, self::FIELD_ID_MEASUREMENT, Measurement::FIELD_ID);
    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
