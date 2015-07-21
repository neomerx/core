<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_feature_property
 * @property int      id_feature
 * @property int      id_language
 * @property string   name
 * @property Feature  $feature
 * @property Language language
 *
 * @package Neomerx\Core
 */
class FeatureProperties extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'feature_properties';

    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID          = 'id_feature_property';
    /** Model field name */
    const FIELD_ID_FEATURE  = Feature::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE = Language::FIELD_ID;
    /** Model field name */
    const FIELD_NAME        = 'name';
    /** Model field name */
    const FIELD_FEATURE     = 'feature';
    /** Model field name */
    const FIELD_LANGUAGE    = 'language';

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
        self::FIELD_NAME,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_FEATURE,
        self::FIELD_ID_LANGUAGE,
    ];
    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_FEATURE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_FEATURE,
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
            self::FIELD_ID_FEATURE  => 'required|integer|min:1|max:4294967295|exists:'.Feature::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_FEATURE  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Feature::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
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
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
