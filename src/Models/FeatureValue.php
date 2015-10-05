<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_feature_value
 * @property      int        id_feature
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Feature    feature
 * @property      Collection properties
 * @property      Collection aspects
 *
 * @package Neomerx\Core
 */
class FeatureValue extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'feature_values';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_feature_value';
    /** Model field name */
    const FIELD_ID_FEATURE = Feature::FIELD_ID;
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';
    /** Model field name */
    const FIELD_FEATURE    = 'feature';
    /** Model field name */
    const FIELD_PROPERTIES = 'properties';
    /** Model field name */
    const FIELD_ASPECTS    = 'aspects';

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
        self::FIELD_ID_FEATURE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_FEATURE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE       => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_ID_FEATURE => 'required|integer|min:1|max:4294967295|exists:'.Feature::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ID_FEATURE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Feature::TABLE_NAME,
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.FeatureValueProperty::FIELD_LANGUAGE;
    }

    /**
     * Relation to feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, self::FIELD_ID_FEATURE, Feature::FIELD_ID);
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
            FeatureValueProperty::class,
            FeatureValueProperty::FIELD_ID_FEATURE_VALUE,
            self::FIELD_ID
        );
    }

    /**
     * Relation to product aspects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aspects()
    {
        return $this->hasMany(Aspect::class, Aspect::FIELD_ID_VALUE, self::FIELD_ID);
    }
}
