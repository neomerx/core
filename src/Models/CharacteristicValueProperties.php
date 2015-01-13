<?php namespace Neomerx\Core\Models;

/**
 * @property int                 id_characteristic_value_property
 * @property int                 id_characteristic_value
 * @property int                 id_language
 * @property string              value
 * @property CharacteristicValue characteristic_value
 * @property Language            language
 */
class CharacteristicValueProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'characteristic_value_properties';

    const VALUE_MAX_LENGTH = 100;

    const FIELD_ID                      = 'id_characteristic_value_property';
    const FIELD_ID_CHARACTERISTIC_VALUE = CharacteristicValue::FIELD_ID;
    const FIELD_ID_LANGUAGE             = Language::FIELD_ID;
    const FIELD_VALUE                   = 'value';
    const FIELD_CHARACTERISTIC_VALUE    = 'characteristic_value';
    const FIELD_LANGUAGE                = 'language';

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
    protected $hidden = [
        self::FIELD_ID_CHARACTERISTIC_VALUE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CHARACTERISTIC_VALUE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $touches = [
        'characteristicValue',
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
            self::FIELD_ID_CHARACTERISTIC_VALUE => 'required|integer|min:1|max:4294967295|exists:' .
                CharacteristicValue::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_VALUE       => 'required|min:1|max:'.self::VALUE_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CHARACTERISTIC_VALUE => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                CharacteristicValue::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_VALUE       => 'sometimes|required|min:1|max:'.self::VALUE_MAX_LENGTH,
        ];
    }

    /**
     * Relation to characteristic value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function characteristicValue()
    {
        return $this->belongsTo(
            CharacteristicValue::BIND_NAME,
            self::FIELD_ID_CHARACTERISTIC_VALUE,
            CharacteristicValue::FIELD_ID
        );
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
