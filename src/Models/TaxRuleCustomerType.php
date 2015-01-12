<?php namespace Neomerx\Core\Models;

/**
 * @property int          id_tax_rule_customer_type
 * @property int          id_tax_rule
 * @property int          id_customer_type
 * @property CustomerType type
 * @property TaxRule      rule
 */
class TaxRuleCustomerType extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'tax_rule_customer_types';

    const FIELD_ID               = 'id_tax_rule_customer_type';
    const FIELD_ID_TAX_RULE      = TaxRule::FIELD_ID;
    const FIELD_ID_CUSTOMER_TYPE = CustomerType::FIELD_ID;
    const FIELD_TYPE             = 'type';
    const FIELD_RULE             = 'rule';

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
    protected $hidden = [
        self::FIELD_ID_CUSTOMER_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX_RULE,
        self::FIELD_ID_CUSTOMER_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE      => 'required|integer|min:1|max:4294967295|exists:' . TaxRule::TABLE_NAME,
            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                CustomerType::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE      => 'sometimes|required|forbidden',
            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                CustomerType::TABLE_NAME,
        ];
    }

    /**
     * Relation to tax rule.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rule()
    {
        return $this->belongsTo(TaxRule::BIND_NAME, self::FIELD_ID_TAX_RULE, TaxRule::FIELD_ID);
    }

    /**
     * Relation to customer type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(CustomerType::BIND_NAME, self::FIELD_ID_CUSTOMER_TYPE, CustomerType::FIELD_ID);
    }
}
