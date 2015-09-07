<?php namespace Neomerx\Core\Models;

/**
 * @property int          id_feature_value_property
 * @property int          id_feature_value
 * @property int          id_language
 * @property string       value
 * @property FeatureValue $featureValue
 * @property Language     language
 *
 * @package Neomerx\Core
 */
class FeatureValueProperty extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'feature_value_properties';

    /** Model field length */
    const VALUE_MAX_LENGTH = 100;

    /** Model field name */
    const FIELD_ID               = 'id_feature_value_property';
    /** Model field name */
    const FIELD_ID_FEATURE_VALUE = FeatureValue::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE      = Language::FIELD_ID;
    /** Model field name */
    const FIELD_VALUE            = 'value';
    /** Model field name */
    const FIELD_FEATURE_VALUE    = 'featureValue';
    /** Model field name */
    const FIELD_LANGUAGE         = 'language';

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
    protected $fillable = [
        self::FIELD_VALUE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_FEATURE_VALUE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_FEATURE_VALUE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_FEATURE_VALUE,
    ];

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_FEATURE_VALUE => 'required|integer|min:1|max:4294967295|exists:'.FeatureValue::TABLE_NAME,
            self::FIELD_ID_LANGUAGE      => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_VALUE            => 'required|min:1|max:'.self::VALUE_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_FEATURE_VALUE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                FeatureValue::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_VALUE       => 'sometimes|required|min:1|max:'.self::VALUE_MAX_LENGTH,
        ];
    }

    /**
     * Relation to feature value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function featureValue()
    {
        return $this->belongsTo(FeatureValue::class, self::FIELD_ID_FEATURE_VALUE, FeatureValue::FIELD_ID);
    }

    /**
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
