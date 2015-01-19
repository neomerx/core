<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_invoice
 * @property string     code
 * @property Collection payments
 * @property Collection orders
 */
class Invoice extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'invoices';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID       = 'id_invoice';
    const FIELD_CODE     = 'code';
    const FIELD_PAYMENTS = 'payments';
    const FIELD_ORDERS   = 'orders';

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
        self::FIELD_CODE,
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
        self::FIELD_CODE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * Relation to invoice payments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(InvoicePayment::BIND_NAME, InvoicePayment::FIELD_ID_INVOICE, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(
            Order::BIND_NAME,
            InvoiceOrder::TABLE_NAME,
            InvoiceOrder::FIELD_ID_INVOICE,
            InvoiceOrder::FIELD_ID_ORDER
        );
    }
}
