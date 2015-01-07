<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_carrier_postcode
 * @property int     id_carrier
 * @property int     postcode_from
 * @property int     postcode_to
 * @property string  postcode_mask
 * @property Carrier carrier
 */
class CarrierPostcode extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'carrier_postcodes';

    const POSTCODE_MASK_MAX_LENGTH = 255;

    const FIELD_ID            = 'id_carrier_postcode';
    const FIELD_ID_CARRIER    = Carrier::FIELD_ID;
    const FIELD_POSTCODE_FROM = 'postcode_from';
    const FIELD_POSTCODE_TO   = 'postcode_to';
    const FIELD_POSTCODE_MASK = 'postcode_mask';
    const FIELD_CARRIER       = 'carrier';

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
        self::FIELD_ID_CARRIER,
        self::FIELD_POSTCODE_FROM,
        self::FIELD_POSTCODE_TO,
        self::FIELD_POSTCODE_MASK,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public static function getInputOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'required|integer|min:1|max:4294967295',
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:' . self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'required|integer|min:1|max:4294967295|exists:' . Carrier::TABLE_NAME,
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:' . self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'sometimes|required|forbidden',
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:' . self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'sometimes|required|forbidden',
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:' . self::POSTCODE_MASK_MAX_LENGTH,
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
}
