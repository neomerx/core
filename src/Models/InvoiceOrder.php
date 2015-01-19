<?php namespace Neomerx\Core\Models;

/**
 * @property int     id_invoice_order
 * @property int     id_invoice
 * @property int     id_order
 * @property Invoice invoice
 * @property Order   order
 */
class InvoiceOrder extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'invoice_order';

    const FIELD_ID         = 'id_invoice_order';
    const FIELD_ID_INVOICE = Invoice::FIELD_ID;
    const FIELD_ID_ORDER   = Order::FIELD_ID;
    const FIELD_INVOICE    = 'invoice';
    const FIELD_ORDER      = 'order';

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
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID_ORDER,
        self::FIELD_ID_INVOICE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'required|integer|min:1|max:4294967295|exists:'.Invoice::TABLE_NAME,
            self::FIELD_ID_ORDER   => 'required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Invoice::TABLE_NAME,
            self::FIELD_ID_ORDER   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Order::TABLE_NAME,
        ];
    }

    /**
     * Relation to invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::BIND_NAME, self::FIELD_ID_INVOICE, Invoice::FIELD_ID);
    }

    /**
     * Relation to order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::BIND_NAME, self::FIELD_ID_ORDER, Order::FIELD_ID);
    }
}
