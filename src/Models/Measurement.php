<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_measurement
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection features
 * @property      Collection properties
 *
 * @package Neomerx\Core
 */
class Measurement extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'measurements';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_measurement';
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';
    /** Model field name */
    const FIELD_FEATURES   = 'features';
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
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.MeasurementProperties::FIELD_LANGUAGE;
    }

    /**
     * Relation to features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function features()
    {
        return $this->hasMany(Feature::class, Feature::FIELD_ID_MEASUREMENT, self::FIELD_ID);
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
            MeasurementProperties::class,
            MeasurementProperties::FIELD_ID_MEASUREMENT,
            self::FIELD_ID
        );
    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
