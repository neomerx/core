<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_carrier_postcode
 * @property int     id_carrier
 * @property int     postcode_from
 * @property int     postcode_to
 * @property string  postcode_mask
 * @property Carrier carrier
 *
 * @package Neomerx\Core
 */
class CarrierPostcode extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'carrier_postcodes';

    /** Model field length */
    const POSTCODE_MASK_MAX_LENGTH = 255;

    /** Model field name */
    const FIELD_ID            = 'id_carrier_postcode';
    /** Model field name */
    const FIELD_ID_CARRIER    = Carrier::FIELD_ID;
    /** Model field name */
    const FIELD_POSTCODE_FROM = 'postcode_from';
    /** Model field name */
    const FIELD_POSTCODE_TO   = 'postcode_to';
    /** Model field name */
    const FIELD_POSTCODE_MASK = 'postcode_mask';
    /** Model field name */
    const FIELD_CARRIER       = 'carrier';

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
        self::FIELD_POSTCODE_TO,
        self::FIELD_POSTCODE_FROM,
        self::FIELD_POSTCODE_MASK,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_CARRIER,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CARRIER,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'required|integer|min:1|max:4294967295|exists:'.Carrier::TABLE_NAME,
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:'.self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CARRIER    => 'sometimes|required|forbidden',
            self::FIELD_POSTCODE_FROM => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_TO   => 'sometimes|required|integer|min:0|max:4294967295',
            self::FIELD_POSTCODE_MASK => 'sometimes|required|min:1|max:'.self::POSTCODE_MASK_MAX_LENGTH,
        ];
    }

    /**
     * @return string
     */
    public static function withCarrier()
    {
        return self::FIELD_CARRIER;
    }

    /**
     * Relation to carrier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class, self::FIELD_ID_CARRIER, Carrier::FIELD_ID);
    }
}
