<?php namespace Neomerx\Core\Models;

/**
 * @property int         id_measurement_property
 * @property int         id_measurement
 * @property int         id_language
 * @property string      name
 * @property Measurement measurement
 * @property Language    language
 *
 * @package Neomerx\Core
 */
class MeasurementProperty extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'measurement_properties';

    /** Model field length */
    const NAME_MAX_LENGTH = 10;

    /** Model field name */
    const FIELD_ID             = 'id_measurement_property';
    /** Model field name */
    const FIELD_ID_MEASUREMENT = Measurement::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE    = Language::FIELD_ID;
    /** Model field name */
    const FIELD_NAME           = 'name';
    /** Model field name */
    const FIELD_MEASUREMENT    = 'measurement';
    /** Model field name */
    const FIELD_LANGUAGE       = 'language';

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
    public $timestamps = false;

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
        self::FIELD_ID_MEASUREMENT,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_MEASUREMENT,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_MEASUREMENT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_MEASUREMENT => 'required|integer|min:1|max:4294967295|exists:'.Measurement::TABLE_NAME,
            self::FIELD_ID_LANGUAGE    => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME           => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_MEASUREMENT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Measurement::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
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
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
