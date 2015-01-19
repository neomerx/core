<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_tax_rule_territory
 * @property int     id_carrier
 * @property int     territory_id
 * @property string  territory_type
 * @property mixed   territory
 * @property Carrier carrier
 */
class CarrierTerritory extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'carrier_territories';

    const FIELD_ID             = 'id_carrier_territory';
    const FIELD_ID_CARRIER     = Carrier::FIELD_ID;
    const FIELD_TERRITORY_ID   = 'territory_id';
    const FIELD_TERRITORY_TYPE = 'territory_type';
    const FIELD_TERRITORY      = 'territory';
    const FIELD_CARRIER        = 'carrier';

    const TERRITORY_TYPE_COUNTRY = Country::BIND_NAME;
    const TERRITORY_TYPE_REGION  = Region::BIND_NAME;

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
        self::FIELD_TERRITORY_ID,
        self::FIELD_TERRITORY_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CARRIER,
        self::FIELD_TERRITORY_ID,
        self::FIELD_TERRITORY_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER   => 'required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,
            self::FIELD_TERRITORY_ID => 'sometimes|required|integer|min:1|max:4294967295',

            self::FIELD_TERRITORY_TYPE => 'required|in:'.
                self::TERRITORY_TYPE_COUNTRY.','.self::TERRITORY_TYPE_REGION,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER   => 'sometimes|required|forbidden',
            self::FIELD_TERRITORY_ID => 'sometimes|required|integer|min:1|max:4294967295',

            self::FIELD_TERRITORY_TYPE => 'sometimes|required|in:'.
                self::TERRITORY_TYPE_COUNTRY.','.self::TERRITORY_TYPE_REGION,
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function territory()
    {
        return $this->morphTo(self::FIELD_TERRITORY);
    }
}
