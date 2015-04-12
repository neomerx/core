<?php namespace Neomerx\Core\Models;

/**
 * @property int            id_characteristic_property
 * @property int            id_characteristic
 * @property int            id_language
 * @property string         name
 * @property Characteristic characteristic
 * @property Language       language
 *
 * @package Neomerx\Core
 */
class CharacteristicProperties extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'characteristic_properties';

    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID                = 'id_characteristic_property';
    /** Model field name */
    const FIELD_ID_CHARACTERISTIC = Characteristic::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE       = Language::FIELD_ID;
    /** Model field name */
    const FIELD_NAME              = 'name';
    /** Model field name */
    const FIELD_CHARACTERISTIC    = 'characteristic';
    /** Model field name */
    const FIELD_LANGUAGE          = 'language';

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
        self::FIELD_ID_CHARACTERISTIC,
        self::FIELD_ID_LANGUAGE,
    ];
    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CHARACTERISTIC,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_CHARACTERISTIC,
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
            self::FIELD_ID_CHARACTERISTIC => 'required|integer|min:1|max:4294967295|exists:'.
                Characteristic::TABLE_NAME,

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
            self::FIELD_ID_CHARACTERISTIC => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Characteristic::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Language::TABLE_NAME,

            self::FIELD_NAME => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * Relation to characteristic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function characteristic()
    {
        return $this->belongsTo(Characteristic::class, self::FIELD_ID_CHARACTERISTIC, Characteristic::FIELD_ID);
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
