<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_region
 * @property int        id_country
 * @property string     code
 * @property string     name
 * @property int        position
 * @property Country    country
 * @property Collection addresses
 *
 * @package Neomerx\Core
 */
class Region extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'regions';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;
    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_region';
    /** Model field name */
    const FIELD_ID_COUNTRY = Country::FIELD_ID;
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_NAME       = 'name';
    /** Model field name */
    const FIELD_POSITION   = 'position';
    /** Model field name */
    const FIELD_COUNTRY    = 'country';
    /** Model field name */
    const FIELD_ADDRESSES  = 'addresses';

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
        self::FIELD_CODE,
        self::FIELD_NAME,
        self::FIELD_POSITION,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_COUNTRY,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_COUNTRY,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_COUNTRY => 'required|integer|min:1|max:4294967295|exists:'.Country::TABLE_NAME,

            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_NAME     => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_POSITION => 'required|integer|min:0|max:65535',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_COUNTRY => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Country::TABLE_NAME,
            self::FIELD_CODE       => 'sometimes|required|forbidden',
            self::FIELD_NAME       => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_POSITION   => 'sometimes|required|integer|min:0|max:65535',
        ];
    }

    /**
     * Relation to country.
     *
     * @return string
     */
    public static function withCountry()
    {
        return self::FIELD_COUNTRY;
    }

    /**
     * Relation to country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, self::FIELD_ID_COUNTRY, Country::FIELD_ID);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, Address::FIELD_ID_REGION, self::FIELD_ID);
    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
