<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property      int     id_invoice_payment
 * @property      int     id_invoice
 * @property      int     amount
 * @property-read Carbon  created_at
 * @property-read Carbon  updated_at
 * @property      Invoice invoice
 *
 * @package Neomerx\Core
 */
class InvoicePayment extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'invoice_payments';

    /** Model field name */
    const FIELD_ID         = 'id_invoice_payment';
    /** Model field name */
    const FIELD_ID_INVOICE = Invoice::FIELD_ID;
    /** Model field name */
    const FIELD_AMOUNT     = 'amount';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';
    /** Model field name */
    const FIELD_INVOICE    = 'invoice';

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
    public $timestamps = true;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_AMOUNT,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_INVOICE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_INVOICE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'required|integer|min:1|max:4294967295|exists:'.Invoice::TABLE_NAME,
            self::FIELD_AMOUNT     => 'required|integer|min:0',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_INVOICE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Invoice::TABLE_NAME,
            self::FIELD_AMOUNT     => 'required|integer|min:0',
        ];
    }

    /**
     * Relation to invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, self::FIELD_ID_INVOICE, Invoice::FIELD_ID);
    }
}
