<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_customer_risk
 * @property string     code
 * @property string     name
 * @property Collection customers
 *
 * @package Neomerx\Core
 */
class CustomerRisk extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'customer_risks';

    /** Model field length */
    const NAME_MAX_LENGTH = 50;
    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID        = 'id_customer_risk';
    /** Model field name */
    const FIELD_CODE      = 'code';
    /** Model field name */
    const FIELD_NAME      = 'name';
    /** Model field name */
    const FIELD_CUSTOMERS = 'customers';

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
        self::FIELD_CODE,
        self::FIELD_NAME,
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
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * Relation to customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, Customer::FIELD_ID_CUSTOMER_RISK, self::FIELD_ID);
    }
}
