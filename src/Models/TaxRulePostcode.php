<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_tax_rule_postcode
 * @property int     id_tax_rule
 * @property int     postcode_from
 * @property int     postcode_to
 * @property string  postcode_mask
 * @property TaxRule rule
 */
class TaxRulePostcode extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'tax_rule_postcodes';

    const POSTCODE_MASK_MAX_LENGTH = 255;

    const FIELD_ID            = 'id_tax_rule_postcode';
    const FIELD_ID_TAX_RULE   = TaxRule::FIELD_ID;
    const FIELD_POSTCODE_FROM = 'postcode_from';
    const FIELD_POSTCODE_TO   = 'postcode_to';
    const FIELD_POSTCODE_MASK = 'postcode_mask';
    const FIELD_RULE          = 'rule';

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
        self::FIELD_POSTCODE_TO,
        self::FIELD_POSTCODE_FROM,
        self::FIELD_POSTCODE_MASK,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX_RULE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE   => 'required|integer|min:1|max:4294967295|exists:'.TaxRule::TABLE_NAME,
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:'.self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE   => 'sometimes|required|forbidden',
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:'.self::POSTCODE_MASK_MAX_LENGTH,
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
}
