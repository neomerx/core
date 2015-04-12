<?php namespace Neomerx\Core\Models;

/**
 * @property int          id_manufacturer_property
 * @property int          id_manufacturer
 * @property int          id_language
 * @property string       name
 * @property string       description
 * @property Manufacturer manufacturer
 * @property Language     language
 *
 * @package Neomerx\Core
 */
class ManufacturerProperties extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'manufacturer_properties';

    /** Model field length */
    const NAME_MAX_LENGTH         = 50;
    /** Model field length */
    const DESCRIPTION_MAX_LENGTH  = 300;

    /** Model field name */
    const FIELD_ID              = 'id_manufacturer_property';
    /** Model field name */
    const FIELD_ID_MANUFACTURER = Manufacturer::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE     = Language::FIELD_ID;
    /** Model field name */
    const FIELD_NAME            = 'name';
    /** Model field name */
    const FIELD_DESCRIPTION     = 'description';
    /** Model field name */
    const FIELD_MANUFACTURER    = 'manufacturer';
    /** Model field name */
    const FIELD_LANGUAGE        = 'language';

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
        self::FIELD_DESCRIPTION,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_MANUFACTURER,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_MANUFACTURER,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_MANUFACTURER,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_MANUFACTURER => 'required|integer|min:1|max:4294967295|exists:'.Manufacturer::TABLE_NAME,
            self::FIELD_ID_LANGUAGE     => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME            => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION     => 'required|min:1|max:'.self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_MANUFACTURER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Manufacturer::TABLE_NAME,

            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION => 'sometimes|required|min:1|max:'.self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * Relation to manufacturer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, self::FIELD_ID_MANUFACTURER, Manufacturer::FIELD_ID);
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
