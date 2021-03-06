<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property      int         id_order
 * @property      int         id_customer
 * @property      int         id_billing_address
 * @property      int         id_shipping_address
 * @property      int         id_order_status
 * @property      int         products_tax
 * @property      string      products_tax_details
 * @property      int         shipping_included_tax
 * @property      int         shipping_cost
 * @property-read Carbon      created_at
 * @property-read Carbon      updated_at
 * @property-read Carbon      deleted_at
 * @property      Customer    customer
 * @property      Address     shippingAddress
 * @property      Address     billingAddress
 * @property      Store       store
 * @property      OrderStatus status
 * @property      Currency    currency
 * @property      Collection  details
 * @property      Collection  history
 * @property      Collection  invoices
 * @property      Collection  shippingOrders
 *
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order extends BaseModel
{
    use SoftDeletes;

    /** Model table name */
    const TABLE_NAME = 'orders';

    /** Model field name */
    const FIELD_ID                    = 'id_order';
    /** Model field name */
    const FIELD_ID_CUSTOMER           = Customer::FIELD_ID;
    /** Model field name */
    const FIELD_ID_BILLING_ADDRESS    = 'id_billing_address';
    /** Model field name */
    const FIELD_ID_SHIPPING_ADDRESS   = 'id_shipping_address';
    /** Model field name */
    const FIELD_ID_STORE              = Store::FIELD_ID;
    /** Model field name */
    const FIELD_ID_ORDER_STATUS       = OrderStatus::FIELD_ID;
    /** Model field name */
    const FIELD_ID_CURRENCY           = Currency::FIELD_ID;
    /** Model field name */
    const FIELD_PRODUCTS_TAX          = 'products_tax';
    /** Model field name */
    const FIELD_SHIPPING_TAX          = 'shipping_tax';
    /** Model field name */
    const FIELD_TAX_DETAILS           = 'tax_details';
    /** Model field name */
    const FIELD_SHIPPING_COST         = 'shipping_cost';
    /** Model field name */
    const FIELD_CREATED_AT            = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT            = 'updated_at';
    /** Model field name */
    const FIELD_DELETED_AT            = 'deleted_at';
    /** Model field name */
    const FIELD_CUSTOMER              = 'customer';
    /** Model field name */
    const FIELD_SHIPPING_ADDRESS      = 'shippingAddress';
    /** Model field name */
    const FIELD_BILLING_ADDRESS       = 'billingAddress';
    /** Model field name */
    const FIELD_STORE                 = 'store';
    /** Model field name */
    const FIELD_STATUS                = 'status';
    /** Model field name */
    const FIELD_CURRENCY              = 'currency';
    /** Model field name */
    const FIELD_DETAILS               = 'details';
    /** Model field name */
    const FIELD_HISTORY               = 'history';
    /** Model field name */
    const FIELD_INVOICES              = 'invoices';
    /** Model field name */
    const FIELD_SHIPPING_ORDERS       = 'shippingOrders';

    /**
     * @inheritdoc
     */
    protected $dates = [self::FIELD_DELETED_AT];

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
        self::FIELD_SHIPPING_TAX,
        self::FIELD_SHIPPING_COST,
        self::FIELD_PRODUCTS_TAX,
        self::FIELD_TAX_DETAILS,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_STORE,
        self::FIELD_ID_ORDER_STATUS,
        self::FIELD_ID_CURRENCY,
    ];

    /**
     * @inheritdoc
     */
    public $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CUSTOMER,
        self::FIELD_ID_BILLING_ADDRESS,
        self::FIELD_ID_SHIPPING_ADDRESS,
        self::FIELD_ID_STORE,
        self::FIELD_ID_ORDER_STATUS,
        self::FIELD_ID_CURRENCY,
        self::FIELD_PRODUCTS_TAX,
        self::FIELD_SHIPPING_TAX,
        self::FIELD_SHIPPING_COST,
        self::FIELD_TAX_DETAILS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER => 'required|integer|min:1|max:4294967295|exists:'.Customer::TABLE_NAME,

            self::FIELD_ID_BILLING_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Address::TABLE_NAME.','.Address::FIELD_ID,

            self::FIELD_ID_SHIPPING_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Address::TABLE_NAME.','.Address::FIELD_ID,

            self::FIELD_ID_STORE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Store::TABLE_NAME,

            self::FIELD_ID_ORDER_STATUS => 'required|integer|min:1|max:4294967295|exists:'.OrderStatus::TABLE_NAME,
            self::FIELD_ID_CURRENCY     => 'required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,
            self::FIELD_SHIPPING_TAX    => 'required|integer|min:0',
            self::FIELD_SHIPPING_COST   => 'required|integer|min:0',
            self::FIELD_PRODUCTS_TAX    => 'required|integer|min:0',
            self::FIELD_TAX_DETAILS     => 'required|min:1',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CUSTOMER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Customer::TABLE_NAME,

            self::FIELD_ID_BILLING_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Address::TABLE_NAME.','.Address::FIELD_ID,

            self::FIELD_ID_SHIPPING_ADDRESS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Address::TABLE_NAME.','.Address::FIELD_ID,

            self::FIELD_ID_STORE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Store::TABLE_NAME,

            self::FIELD_ID_ORDER_STATUS => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                OrderStatus::TABLE_NAME,

            self::FIELD_ID_CURRENCY   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Currency::TABLE_NAME,
            self::FIELD_SHIPPING_TAX  => 'sometimes|required|integer|min:0',
            self::FIELD_SHIPPING_COST => 'sometimes|required|integer|min:0',
            self::FIELD_PRODUCTS_TAX  => 'sometimes|required|integer|min:0',
            self::FIELD_TAX_DETAILS   => 'sometimes|required_with:'.self::FIELD_PRODUCTS_TAX . '|min:1',
        ];
    }

    /**
     * @return string
     */
    public static function withCustomer()
    {
        return self::FIELD_CUSTOMER;
    }

    /**
     * @return string
     */
    public static function withBillingAddress()
    {
        return self::FIELD_BILLING_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * @return string
     */
    public static function withShippingAddress()
    {
        return self::FIELD_SHIPPING_ADDRESS.'.'.Address::FIELD_REGION.'.'.Region::FIELD_COUNTRY;
    }

    /**
     * @return string
     */
    public static function withStore()
    {
        return self::FIELD_STORE;
    }

    /**
     * @return string
     */
    public static function withStatus()
    {
        return self::FIELD_STATUS;
    }

    /**
     * @return string
     */
    public static function withCurrency()
    {
        return self::FIELD_CURRENCY;
    }

    /**
     * Relation to customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, self::FIELD_ID_CUSTOMER, Customer::FIELD_ID);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, self::FIELD_ID_SHIPPING_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billingAddress()
    {
        return $this->belongsTo(Address::class, self::FIELD_ID_BILLING_ADDRESS, Address::FIELD_ID);
    }

    /**
     * Relation to store.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class, self::FIELD_ID_STORE, Store::FIELD_ID);
    }

    /**
     * Relation to order status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, self::FIELD_ID_ORDER_STATUS, OrderStatus::FIELD_ID);
    }

    /**
     * Relation to currency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, self::FIELD_ID_CURRENCY, Currency::FIELD_ID);
    }

    /**
     * Relation to order details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(OrderDetails::class, OrderDetails::FIELD_ID_ORDER, self::FIELD_ID);
    }

    /**
     * Relation to order history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(OrderHistory::class, OrderHistory::FIELD_ID_ORDER, self::FIELD_ID);
    }

    /**
     * Relation to invoices. It is either empty collection or collection with one invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invoices()
    {
        return $this->belongsToMany(
            Invoice::class,
            InvoiceOrder::TABLE_NAME,
            InvoiceOrder::FIELD_ID_ORDER,
            InvoiceOrder::FIELD_ID_INVOICE
        );
    }

    /**
     * @inheritdoc
     *
     * Stores order status history on changes.
     */
    protected function onUpdating()
    {
        // history saved if necessary
        $historySaved     = true;
        $parentOnUpdating = parent::onUpdating();
        // if id_order_status has changed we should log it to history
        if ($parentOnUpdating === true && $this->isDirty(self::FIELD_ID_ORDER_STATUS) === true) {
            $oldIdOrderStatus = $this->getOriginal(self::FIELD_ID_ORDER_STATUS);
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var \Neomerx\Core\Models\OrderHistory $orderHistory */
            $orderHistory = App::make(OrderHistory::class);
            $orderHistory->{OrderHistory::FIELD_ID_ORDER_STATUS} = $oldIdOrderStatus;
            $historySaved = (false !== $this->history()->save($orderHistory));
        }
        return $parentOnUpdating && $historySaved;
    }
}
