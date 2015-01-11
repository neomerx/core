<?php namespace Neomerx\Core\Models;

/**
 * @property int            id_characteristic_property
 * @property int            id_characteristic
 * @property int            id_language
 * @property string         name
 * @property Characteristic characteristic
 * @property Language       language
 */
class CharacteristicProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'characteristic_properties';

    const NAME_MAX_LENGTH = 50;

    const FIELD_ID                = 'id_characteristic_property';
    const FIELD_ID_CHARACTERISTIC = Characteristic::FIELD_ID;
    const FIELD_ID_LANGUAGE       = Language::FIELD_ID;
    const FIELD_NAME              = 'name';
    const FIELD_CHARACTERISTIC    = 'characteristic';
    const FIELD_LANGUAGE          = 'language';

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
    protected $fillable = [
        self::FIELD_ID_CHARACTERISTIC,
        self::FIELD_ID_LANGUAGE,
        self::FIELD_NAME,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_CHARACTERISTIC,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $touches = [
        self::FIELD_CHARACTERISTIC,
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CHARACTERISTIC => 'required|integer|min:1|max:4294967295|exists:' .
                Characteristic::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:' . Language::TABLE_NAME,
            self::FIELD_NAME        => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CHARACTERISTIC => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                Characteristic::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                Language::TABLE_NAME,

            self::FIELD_NAME => 'sometimes|required|min:1|max:' . self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * Relation to characteristic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function characteristic()
    {
        return $this->belongsTo(Characteristic::BIND_NAME, self::FIELD_ID_CHARACTERISTIC, Characteristic::FIELD_ID);
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
