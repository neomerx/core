<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;

/**
 * @property int         id_supply_order_details
 * @property int         id_supply_order
 * @property int         id_variant
 * @property float       price_wo_tax
 * @property int         quantity
 * @property float       discount_rate
 * @property float       tax_rate
 * @property SupplyOrder supply_order
 * @property Variant     variant
 */
class SupplyOrderDetails extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'supply_order_details';

    const FIELD_ID              = 'id_supply_order_details';
    const FIELD_ID_SUPPLY_ORDER = 'id_supply_order';
    const FIELD_ID_VARIANT      = Variant::FIELD_ID;
    const FIELD_PRICE_WO_TAX    = 'price_wo_tax';
    const FIELD_QUANTITY        = 'quantity';
    const FIELD_DISCOUNT_RATE   = 'discount_rate';
    const FIELD_TAX_RATE        = 'tax_rate';
    const FIELD_SUPPLY_ORDER    = 'supply_order';
    const FIELD_ITEM            = 'item';

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
        self::FIELD_ID_VARIANT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_SUPPLY_ORDER,
        self::FIELD_ID_VARIANT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_SUPPLY_ORDER => 'required|integer|min:1|max:4294967295|exists:'.SupplyOrder::TABLE_NAME,
            self::FIELD_ID_VARIANT      => 'required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_PRICE_WO_TAX    => 'required|numeric|min:0',
            self::FIELD_QUANTITY        => 'required|integer|min:1|max:4294967295',
            self::FIELD_DISCOUNT_RATE   => 'sometimes|required|numeric|min:0|max:100',
            self::FIELD_TAX_RATE        => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_SUPPLY_ORDER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                SupplyOrder::TABLE_NAME,

            self::FIELD_ID_VARIANT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,

            self::FIELD_PRICE_WO_TAX  => 'sometimes|required|numeric|min:0',
            self::FIELD_QUANTITY      => 'sometimes|required|integer|min:1|max:4294967295',
            self::FIELD_DISCOUNT_RATE => 'sometimes|required|numeric|min:0|max:100',
            self::FIELD_TAX_RATE      => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * Relation to supply order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyOrder()
    {
        return $this->belongsTo(SupplyOrder::BIND_NAME, self::FIELD_ID_SUPPLY_ORDER, SupplyOrder::FIELD_ID);
    }

    /**
     * Relation to variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::BIND_NAME, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }
}
