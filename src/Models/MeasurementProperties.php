<?php namespace Neomerx\Core\Models;

/**
 * @property int         id_measurement_property
 * @property int         id_measurement
 * @property int         id_language
 * @property string      name
 * @property Measurement measurement
 * @property Language    language
 */
class MeasurementProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'measurement_properties';

    const NAME_MAX_LENGTH = 10;

    const FIELD_ID             = 'id_measurement_property';
    const FIELD_ID_MEASUREMENT = Measurement::FIELD_ID;
    const FIELD_ID_LANGUAGE    = Language::FIELD_ID;
    const FIELD_NAME           = 'name';
    const FIELD_MEASUREMENT    = 'measurement';
    const FIELD_LANGUAGE       = 'language';

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
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_ID_MEASUREMENT,
        self::FIELD_ID_LANGUAGE,
        self::FIELD_NAME,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_MEASUREMENT,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $touches = [
        self::FIELD_MEASUREMENT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_MEASUREMENT => 'required|integer|min:1|max:4294967295|exists:' . Measurement::TABLE_NAME,
            self::FIELD_ID_LANGUAGE    => 'required|integer|min:1|max:4294967295|exists:' . Language::TABLE_NAME,
            self::FIELD_NAME           => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_MEASUREMENT => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                Measurement::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
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
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::BIND_NAME, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
