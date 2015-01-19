<?php namespace Neomerx\Core\Models;

/**
 * @property int          id_carrier_customer_type
 * @property int          id_carrier
 * @property int          id_customer_type
 * @property CustomerType type
 * @property Carrier      carrier
 */
class CarrierCustomerType extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'carrier_customer_types';

    const FIELD_ID               = 'id_carrier_customer_type';
    const FIELD_ID_CARRIER       = Carrier::FIELD_ID;
    const FIELD_ID_CUSTOMER_TYPE = CustomerType::FIELD_ID;
    const FIELD_TYPE             = 'type';
    const FIELD_CARRIER          = 'carrier';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_CUSTOMER_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CARRIER,
        self::FIELD_ID_CUSTOMER_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER       => 'required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,

            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerType::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER       => 'sometimes|required|forbidden',

            self::FIELD_ID_CUSTOMER_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                CustomerType::TABLE_NAME,
        ];
    }

    /**
     * Relation to carrier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::BIND_NAME, self::FIELD_ID_CARRIER, Carrier::FIELD_ID);
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
