<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_currency
 * @property string     code
 * @property int        decimal_digits
 * @property Collection properties
 *
 * @link https://en.wikipedia.org/wiki/ISO_4217
 */
class Currency extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'currencies';

    const CODE_MIN_LENGTH = 3;
    const CODE_MAX_LENGTH = 3;

    const FIELD_ID             = 'id_currency';
    const FIELD_CODE           = 'code';
    const FIELD_DECIMAL_DIGITS = 'decimal_digits';
    const FIELD_PROPERTIES     = 'properties';

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
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_ID,
        self::FIELD_CODE,
        self::FIELD_DECIMAL_DIGITS,
    ];

    public $guarded = [
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID => 'required|integer|min:1|max:999'.'|unique:'.self::TABLE_NAME,

            self::FIELD_CODE => 'required|code|min:'.self::CODE_MIN_LENGTH.
                '|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_DECIMAL_DIGITS => 'required|integer|min:0|max:4',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID             => 'sometimes|required|forbidden',
            self::FIELD_CODE           => 'sometimes|required|forbidden',
            self::FIELD_DECIMAL_DIGITS => 'sometimes|required|integer|min:0|max:4',
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CurrencyProperties::FIELD_LANGUAGE;
    }

    /**
     * Relation to currency language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CurrencyProperties::BIND_NAME, CurrencyProperties::FIELD_ID_CURRENCY, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
