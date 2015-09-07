<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_currency
 * @property string     code
 * @property int        decimal_digits
 * @property Collection properties
 *
 * @link https://en.wikipedia.org/wiki/ISO_4217
 *
 * @package Neomerx\Core
 */
class Currency extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'currencies';

    /** Model field length */
    const CODE_MIN_LENGTH = 3;
    /** Model field length */
    const CODE_MAX_LENGTH = 3;
    /** Model field length */
    const NUMBER_MIN = 1;
    /** Model field length */
    const NUMBER_MAX = 999;

    /** Model field name */
    const FIELD_ID                       = 'id_currency';
    /** Model field name */
    const FIELD_NUMBER                   = 'number';
    /** Model field name */
    const FIELD_CODE                     = 'code';
    /** Model field name */
    const FIELD_DECIMAL_DIGITS           = 'decimal_digits';
    /** Model field name */
    const FIELD_DECIMAL_POINTS_SEPARATOR = 'decimal_point_separator';
    /** Model field name */
    const FIELD_THOUSANDS_SEPARATOR      = 'thousands_separator';
    /** Model field name */
    const FIELD_PROPERTIES               = 'properties';

    /** Default value */
    const DECIMAL_POINTS_SEPARATOR_DEFAULT = '.';
    /** Default value */
    const THOUSANDS_SEPARATOR_DEFAULT      = ',';

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
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_CODE,
        self::FIELD_NUMBER,
        self::FIELD_DECIMAL_DIGITS,
        self::FIELD_DECIMAL_POINTS_SEPARATOR,
        self::FIELD_THOUSANDS_SEPARATOR,
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
    public $guarded = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_NUMBER => 'required|integer|min:'.self::NUMBER_MIN.
                '|max:'.self::NUMBER_MAX.'|unique:'.self::TABLE_NAME,

            self::FIELD_CODE => 'required|code|min:'.self::CODE_MIN_LENGTH.
                '|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_DECIMAL_DIGITS           => 'required|integer|min:0|max:4',
            self::FIELD_DECIMAL_POINTS_SEPARATOR => 'required|string|size:1',
            self::FIELD_THOUSANDS_SEPARATOR      => 'string|min:0|max:1',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID                       => 'sometimes|required|forbidden',
            self::FIELD_CODE                     => 'sometimes|required|forbidden',
            self::FIELD_DECIMAL_DIGITS           => 'sometimes|required|integer|min:0|max:4',
            self::FIELD_DECIMAL_POINTS_SEPARATOR => 'sometimes|required|string|size:1',
            self::FIELD_THOUSANDS_SEPARATOR      => 'sometimes|string|min:0|max:1',
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CurrencyProperty::FIELD_LANGUAGE;
    }

    /**
     * Relation to currency language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CurrencyProperty::class, CurrencyProperty::FIELD_ID_CURRENCY, self::FIELD_ID);
    }
}
