<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_country
 * @property      string     code
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection properties
 * @property      Collection regions
 *
 * @package Neomerx\Core
 */
class Country extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'countries';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID         = 'id_country';
    /** Model field name */
    const FIELD_CODE       = 'code';
    /** Model field name */
    const FIELD_PROPERTIES = 'properties';
    /** Model field name */
    const FIELD_REGIONS    = 'regions';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';

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
    public $timestamps = true;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_CODE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * Relation to country.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CountryProperties::FIELD_LANGUAGE;
    }

    /**
     * Relation to country language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CountryProperties::class, CountryProperties::FIELD_ID_COUNTRY, self::FIELD_ID);
    }

    /**
     * Relation to country regions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany(Region::class, Region::FIELD_ID_COUNTRY, self::FIELD_ID);
    }
}
