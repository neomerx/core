<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_invoice
 * @property string     code
 * @property Collection payments
 * @property Collection orders
 *
 * @package Neomerx\Core
 */
class Invoice extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'invoices';

    /** Model field length */
    const CODE_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID       = 'id_invoice';
    /** Model field name */
    const FIELD_CODE     = 'code';
    /** Model field name */
    const FIELD_PAYMENTS = 'payments';
    /** Model field name */
    const FIELD_ORDERS   = 'orders';

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
        self::FIELD_CODE,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_CODE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
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
        return $this->hasMany(InvoicePayment::class, InvoicePayment::FIELD_ID_INVOICE, self::FIELD_ID);
    }

    /**
     * Relation to orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(
            Order::class,
            InvoiceOrder::TABLE_NAME,
            InvoiceOrder::FIELD_ID_INVOICE,
            InvoiceOrder::FIELD_ID_ORDER
        );
    }
}
