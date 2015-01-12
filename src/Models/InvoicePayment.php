<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int     id_invoice_payment
 * @property      int     id_invoice
 * @property      float   amount
 * @property-read Carbon  created_at
 * @property-read Carbon  updated_at
 * @property      Invoice invoice
 */
class InvoicePayment extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'invoice_payments';

    const FIELD_ID         = 'id_invoice_payment';
    const FIELD_ID_INVOICE = Invoice::FIELD_ID;
    const FIELD_AMOUNT     = 'amount';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';
    const FIELD_INVOICE    = 'invoice';

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
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_INVOICE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_INVOICE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'required|integer|min:1|max:4294967295|exists:' . Invoice::TABLE_NAME,
            self::FIELD_AMOUNT     => 'required|numeric|min:0',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Invoice::TABLE_NAME,
            self::FIELD_AMOUNT     => 'required|numeric|min:0',
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
}
