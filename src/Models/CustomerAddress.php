<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_customer_address
 * @property int      id_customer
 * @property int      id_address
 * @property string   type
 * @property bool     is_default
 * @property Customer customer
 * @property Address  address
 */
class CustomerAddress extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'customers_addresses';

    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';
    const IS_DEFAULT    = 'default';

    const FIELD_ID          = 'id_customer_address';
    const FIELD_ID_ADDRESS  = Address::FIELD_ID;
    const FIELD_ID_CUSTOMER = Customer::FIELD_ID;
    const FIELD_TYPE        = 'type';
    const FIELD_IS_DEFAULT  = 'is_default';
    const FIELD_CUSTOMER    = 'customer';
    const FIELD_ADDRESS     = 'address';

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
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CUSTOMER,
        self::FIELD_ID_ADDRESS,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER => 'required|integer|min:1|max:4294967295|exists:'.Customer::TABLE_NAME,
            self::FIELD_ID_ADDRESS  => 'required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
            self::FIELD_TYPE        => 'required|in:'.self::TYPE_BILLING.','.self::TYPE_SHIPPING,
            self::FIELD_IS_DEFAULT  => 'sometimes|required|in:'.self::IS_DEFAULT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Customer::TABLE_NAME,
            self::FIELD_ID_ADDRESS  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Address::TABLE_NAME,
            self::FIELD_TYPE        => 'sometimes|required|in:'.self::TYPE_BILLING.','.self::TYPE_SHIPPING,
            self::FIELD_IS_DEFAULT  => 'sometimes|required|in:'.self::IS_DEFAULT,
        ];
    }

    /**
     * Relation to customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::BIND_NAME, self::FIELD_ID_CUSTOMER, Customer::FIELD_ID);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::BIND_NAME, self::FIELD_ID_ADDRESS, Address::FIELD_ID);
    }

    /**
     * @param bool $value
     */
    public function setIsDefaultAttribute($value)
    {
        if ((bool)$value === true) {
            $this->attributes[self::FIELD_IS_DEFAULT] = self::IS_DEFAULT;
        } else {
            unset($this->attributes[self::FIELD_IS_DEFAULT]);
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDefaultAttribute($value)
    {
        return isset($value);
    }
}
