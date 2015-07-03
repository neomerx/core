<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_tax_rule
 * @property      int        id_tax
 * @property      string     name
 * @property      int        priority
 * @property      Tax        tax
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection territories
 * @property      Collection postcodes
 * @property      Collection customerTypes
 * @property      Collection productTypes
 *
 * @package Neomerx\Core
 */
class TaxRule extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'tax_rules';

    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID             = 'id_tax_rule';
    /** Model field name */
    const FIELD_ID_TAX         = Tax::FIELD_ID;
    /** Model field name */
    const FIELD_NAME           = 'name';
    /** Model field name */
    const FIELD_PRIORITY       = 'priority';
    /** Model field name */
    const FIELD_CREATED_AT     = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT     = 'updated_at';
    /** Model field name */
    const FIELD_TAX            = 'tax';
    /** Model field name */
    const FIELD_TERRITORIES    = 'territories';
    /** Model field name */
    const FIELD_POSTCODES      = 'postcodes';
    /** Model field name */
    const FIELD_CUSTOMER_TYPES = 'customerTypes';
    /** Model field name */
    const FIELD_PRODUCT_TYPES  = 'productTypes';

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
        self::FIELD_NAME,
        self::FIELD_PRIORITY,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_TAX,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_NAME     => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_PRIORITY => 'required|integer|min:1|max:4294967295',
            self::FIELD_ID_TAX   => 'required|integer|min:1|max:4294967295|exists:'.Tax::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_NAME     => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_PRIORITY => 'sometimes|required|integer|min:1|max:4294967295|',
            self::FIELD_ID_TAX   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Tax::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withTax()
    {
        return self::FIELD_TAX;
    }

    /**
     * @return string
     */
    public static function withTerritories()
    {
        return self::FIELD_TERRITORIES;
    }

    /**
     * @return string
     */
    public static function withPostcodes()
    {
        return self::FIELD_POSTCODES;
    }

    /**
     * @return string
     */
    public static function withCustomerTypes()
    {
        return self::FIELD_CUSTOMER_TYPES.'.'.TaxRuleCustomerType::FIELD_TYPE;
    }

    /**
     * @return string
     */
    public static function withProductTypes()
    {
        return self::FIELD_PRODUCT_TYPES.'.'.TaxRuleProductType::FIELD_TYPE;
    }

    /**
     * Relation to tax.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class, self::FIELD_ID_TAX, Tax::FIELD_ID);
    }

    /**
     * Relation to rule territories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function territories()
    {
        return $this->hasMany(TaxRuleTerritory::class, TaxRuleTerritory::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule postcodes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postcodes()
    {
        return $this->hasMany(TaxRulePostcode::class, TaxRulePostcode::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule customer types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerTypes()
    {
        return $this->hasMany(TaxRuleCustomerType::class, TaxRuleCustomerType::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule product types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productTypes()
    {
        return $this->hasMany(TaxRuleProductType::class, TaxRuleProductType::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }
}
