<?php namespace Neomerx\Core\Models;

/**
 * @property int            id_tax_rule_product_type
 * @property int            id_tax_rule
 * @property int            id_product_tax_type
 * @property ProductTaxType type
 * @property TaxRule        rule
 *
 * @package Neomerx\Core
 */
class TaxRuleProductType extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'tax_rule_product_types';

    /** Model field name */
    const FIELD_ID                  = 'id_tax_rule_product_type';
    /** Model field name */
    const FIELD_ID_TAX_RULE         = TaxRule::FIELD_ID;
    /** Model field name */
    const FIELD_ID_PRODUCT_TAX_TYPE = ProductTaxType::FIELD_ID;
    /** Model field name */
    const FIELD_TYPE                = 'type';
    /** Model field name */
    const FIELD_RULE                = 'rule';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT_TAX_TYPE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX_RULE,
        self::FIELD_ID_PRODUCT_TAX_TYPE,
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
            self::FIELD_ID_TAX_RULE         => 'required|integer|min:1|max:4294967295|exists:'.TaxRule::TABLE_NAME,

            self::FIELD_ID_PRODUCT_TAX_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ProductTaxType::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE         => 'sometimes|required|forbidden',

            self::FIELD_ID_PRODUCT_TAX_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ProductTaxType::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withRule()
    {
        return self::FIELD_RULE;
    }

    /**
     * @return string
     */
    public static function withType()
    {
        return self::FIELD_TYPE;
    }

    /**
     * Relation to tax rule.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rule()
    {
        return $this->belongsTo(TaxRule::class, self::FIELD_ID_TAX_RULE, TaxRule::FIELD_ID);
    }

    /**
     * Relation to product tax type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ProductTaxType::class, self::FIELD_ID_PRODUCT_TAX_TYPE, ProductTaxType::FIELD_ID);
    }
}
