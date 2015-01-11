<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_tax_rule
 * @property int        id_tax
 * @property string     name
 * @property int        priority
 * @property Tax        tax
 * @property Collection territories
 * @property Collection postcodes
 * @property Collection customer_types
 * @property Collection product_types
 */
class TaxRule extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'tax_rules';

    const NAME_MAX_LENGTH = 50;

    const FIELD_ID             = 'id_tax_rule';
    const FIELD_ID_TAX         = Tax::FIELD_ID;
    const FIELD_NAME           = 'name';
    const FIELD_PRIORITY       = 'priority';
    const FIELD_TAX            = 'tax';
    const FIELD_TERRITORIES    = 'territories';
    const FIELD_POSTCODES      = 'postcodes';
    const FIELD_CUSTOMER_TYPES = 'customer_types';
    const FIELD_PRODUCT_TYPES  = 'product_types';

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
        self::FIELD_NAME,
        self::FIELD_PRIORITY,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_TAX,
    ];

    // TODO I think all models should have hidden fields as guarded. Why links to 'coded' models could be fillable?

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_NAME     => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
            self::FIELD_PRIORITY => 'required|integer|min:1|max:4294967295',
            self::FIELD_ID_TAX   => 'required|integer|min:1|max:4294967295|exists:' . Tax::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_NAME     => 'sometimes|required|min:1|max:' . self::NAME_MAX_LENGTH,
            self::FIELD_PRIORITY => 'sometimes|required|integer|min:1|max:4294967295|',
            self::FIELD_ID_TAX   => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Tax::TABLE_NAME,
        ];
    }

    /**
     * Relation to tax.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax()
    {
        return $this->belongsTo(Tax::BIND_NAME, self::FIELD_ID_TAX, Tax::FIELD_ID);
    }

    /**
     * Relation to rule territories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function territories()
    {
        return $this->hasMany(TaxRuleTerritory::BIND_NAME, TaxRuleTerritory::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule postcodes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postcodes()
    {
        return $this->hasMany(TaxRulePostcode::BIND_NAME, TaxRulePostcode::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule customer types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerTypes()
    {
        return $this->hasMany(TaxRuleCustomerType::BIND_NAME, TaxRuleCustomerType::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }

    /**
     * Relation to rule product types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productTypes()
    {
        return $this->hasMany(TaxRuleProductType::BIND_NAME, TaxRuleProductType::FIELD_ID_TAX_RULE, self::FIELD_ID);
    }
}
