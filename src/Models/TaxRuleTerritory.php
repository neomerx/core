<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_tax_rule_territory
 * @property int     id_tax_rule
 * @property int     territory_id
 * @property string  territory_type
 * @property mixed   territory
 * @property TaxRule rule
 *
 * @package Neomerx\Core
 */
class TaxRuleTerritory extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'tax_rule_territories';

    /** Model field name */
    const FIELD_ID             = 'id_tax_rule_territory';
    /** Model field name */
    const FIELD_ID_TAX_RULE    = TaxRule::FIELD_ID;
    /** Model field name */
    const FIELD_TERRITORY_ID   = 'territory_id';
    /** Model field name */
    const FIELD_TERRITORY_TYPE = 'territory_type';
    /** Model field name */
    const FIELD_TERRITORY      = 'territory';
    /** Model field name */
    const FIELD_RULE           = 'rule';

    /** Territory type code */
    const TERRITORY_TYPE_COUNTRY = Country::class;
    /** Territory type code */
    const TERRITORY_TYPE_REGION  = Region::class;

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_TERRITORY_ID,
        self::FIELD_TERRITORY_TYPE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_TAX_RULE,
        self::FIELD_TERRITORY_ID,
        self::FIELD_TERRITORY_TYPE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE  => 'required|integer|min:1|max:4294967295|exists:'.TaxRule::TABLE_NAME,
            self::FIELD_TERRITORY_ID => 'sometimes|required|integer|min:1|max:4294967295',

            self::FIELD_TERRITORY_TYPE => 'required|in:'.
                self::TERRITORY_TYPE_COUNTRY.','.self::TERRITORY_TYPE_REGION,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_TAX_RULE  => 'sometimes|required|forbidden',
            self::FIELD_TERRITORY_ID => '',

            self::FIELD_TERRITORY_TYPE => 'sometimes|required|in:'.
                self::TERRITORY_TYPE_COUNTRY.','.self::TERRITORY_TYPE_REGION,
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
    public static function withTerritory()
    {
        return self::FIELD_TERRITORY;
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function territory()
    {
        return $this->morphTo(self::FIELD_TERRITORY);
    }
}
