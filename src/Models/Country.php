<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_country
 * @property string     code
 * @property Collection properties
 * @property Collection regions
 * @method   Builder    withProperties()
 */
class Country extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'countries';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID         = 'id_country';
    const FIELD_CODE       = 'code';
    const FIELD_PROPERTIES = 'properties';
    const FIELD_REGIONS    = 'regions';

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
        self::FIELD_CODE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public static function getInputOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithProperties(Builder $query)
    {
        return $query->with([self::FIELD_PROPERTIES.'.'.CountryProperties::FIELD_LANGUAGE]);
    }

    /**
     * Relation to country language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CountryProperties::BIND_NAME, CountryProperties::FIELD_ID_COUNTRY, self::FIELD_ID);
    }

    /**
     * Relation to country regions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany(Region::BIND_NAME, Region::FIELD_ID_COUNTRY, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
