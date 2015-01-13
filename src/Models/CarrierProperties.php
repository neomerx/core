<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_carrier_property
 * @property int      id_carrier
 * @property int      id_language
 * @property string   name
 * @property string   description
 * @property Carrier  carrier
 * @property Language language
 */
class CarrierProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'carrier_properties';

    const NAME_MAX_LENGTH        = 50;
    const DESCRIPTION_MAX_LENGTH = 300;

    const FIELD_ID          = 'id_carrier_property';
    const FIELD_ID_CARRIER  = Carrier::FIELD_ID;
    const FIELD_ID_LANGUAGE = Language::FIELD_ID;
    const FIELD_NAME        = 'name';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_CARRIER     = 'carrier';
    const FIELD_LANGUAGE    = 'language';

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
    protected $hidden = [
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER  => 'required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'required|alpha_dash_dot_space|min:1|max:'     .self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION => 'required|alpha_dash_dot_space|min:1|max:'     .self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.self::NAME_MAX_LENGTH,

            self::FIELD_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:' .
                self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * Relation to carrier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::BIND_NAME, self::FIELD_ID_CARRIER, Carrier::FIELD_ID);
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
