<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @property int        id_tax
 * @property string     code
 * @property string     expression
 * @property string     expression_serialized
 * @property bool       is_formula
 * @property Collection rules
 */
class Tax extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'taxes';

    const CODE_MAX_LENGTH = 50;

    const EXPRESSION_LANGUAGE_CLASS = ExpressionLanguage::class;

    const PARAM_ADDRESS_TO     = 'address_to';
    const PARAM_ADDRESS_FROM   = 'address_from';
    const PARAM_CUMULATIVE_TAX = 'cumulative_tax';
    const PARAM_CUSTOMER       = 'customer';
    const PARAM_PRODUCT        = 'product';
    const PARAM_QUANTITY       = 'quantity';
    const PARAM_PRICE          = 'price';

    const FIELD_ID                    = 'id_tax';
    const FIELD_CODE                  = 'code';
    const FIELD_EXPRESSION            = 'expression';
    const FIELD_IS_FORMULA            = 'is_formula';
    const FIELD_EXPRESSION_SERIALIZED = 'expression_serialized';
    const FIELD_RULES                 = 'rules';

    private static $allowedNamesInFormula = [
        self::PARAM_ADDRESS_TO,
        self::PARAM_ADDRESS_FROM,
        self::PARAM_CUSTOMER,
        self::PARAM_CUMULATIVE_TAX,
        self::PARAM_PRODUCT,
        self::PARAM_QUANTITY,
    ];

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
        self::FIELD_ID,
        self::FIELD_EXPRESSION_SERIALIZED,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_EXPRESSION_SERIALIZED,
    ];

    /**
     * {@inheritdoc}
     */
    protected $appends = [
        self::FIELD_IS_FORMULA,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:' . self::CODE_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,

            self::FIELD_EXPRESSION            => 'required',
            self::FIELD_EXPRESSION_SERIALIZED => 'required',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE                  => 'sometimes|required|forbidden',
            self::FIELD_EXPRESSION            => '',
            self::FIELD_EXPRESSION_SERIALIZED => 'required_with:' . self::FIELD_EXPRESSION,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }

    /**
     * Relation to tax rules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules()
    {
        return $this->hasMany(TaxRule::BIND_NAME, TaxRule::FIELD_ID_TAX, self::FIELD_ID);
    }

    /**
     * Check if tax calculation contains formula.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsFormulaAttribute()
    {
        return $this->isFormula($this->attributes[self::FIELD_EXPRESSION]);
    }

    /**
     * @param string $value
     */
    public function setExpressionAttribute($value)
    {
        $value = trim($value);
        $value = empty($value) ? null : $value;

        if ($this->isFormula($value)) {
            /** @var ExpressionLanguage $expLang */
            /** @noinspection PhpUndefinedMethodInspection */
            $expLang = App::make(self::EXPRESSION_LANGUAGE_CLASS);
            $parsed = $expLang->parse(substr($value, 1), self::$allowedNamesInFormula);
            $this->attributes[self::FIELD_EXPRESSION_SERIALIZED] = serialize($parsed);
        } else {
            $this->attributes[self::FIELD_EXPRESSION_SERIALIZED] = $value;
        }

        $this->attributes[self::FIELD_EXPRESSION] = $value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isFormula($value)
    {
        return (!empty($value) and trim($value)[0] === '=');
    }

    /**
     * @param int   $countryId
     * @param int   $regionId
     * @param mixed $postcode
     * @param int   $customerTypeId
     * @param int   $productTaxTypeId
     *
     * @return Collection
     */
    public function selectTaxes(
        $countryId,
        $regionId,
        $postcode,
        $customerTypeId,
        $productTaxTypeId
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->convertStdClassesToModels(DB::select(
            'call spSelectTaxes(?, ?, ?, ?, ?)',
            [$countryId, $regionId, $postcode, $customerTypeId, $productTaxTypeId]
        ));
    }

    /**
     * Calculate tax.
     *
     * @param array $values
     *
     * @return float
     */
    public function calculate(array $values)
    {
        $quantity =  S\array_get_value($values, self::PARAM_QUANTITY);
        $quantity ?: S\throwEx(new InvalidArgumentException(self::PARAM_QUANTITY));
        settype($quantity, 'float');

        if ($this->getIsFormulaAttribute()) {
            $expressionSerialized = $this->attributes[self::FIELD_EXPRESSION_SERIALIZED];
            /** @var ExpressionLanguage $expLang */
            /** @noinspection PhpUndefinedMethodInspection */
            $expLang = App::make(self::EXPRESSION_LANGUAGE_CLASS);
            $result  = (float)$expLang->evaluate(unserialize($expressionSerialized), $values);
        } else {
            // product must be there
            $price =  S\array_get_value($values, self::PARAM_PRICE);
            $price ?: S\throwEx(new InvalidArgumentException(self::PARAM_PRICE));
            settype($price, 'float');

            $percent = (float)$this->attributes[self::FIELD_EXPRESSION];

            $result = $price * $percent / 100.0;
        }

        return $result * $quantity;
    }
}
