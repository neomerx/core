<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_product_tax_type
 * @property string     code
 * @property string     name
 * @property Collection products
 */
class ProductTaxType extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'product_tax_types';

    const NAME_MAX_LENGTH = 50;
    const CODE_MAX_LENGTH = 50;

    const FIELD_ID       = 'id_product_tax_type';
    const FIELD_CODE     = 'code';
    const FIELD_NAME     = 'name';
    const FIELD_PRODUCTS = 'products';

    const SHIPPING_ID   = 1;
    const SHIPPING_CODE = 'SHIPPING';
    const EXEMPT_ID     = 2;
    const EXEMPT_CODE   = 'EXEMPT';
    const TAXABLE_ID    = 3;
    const TAXABLE_CODE  = 'TAXABLE';

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
    protected $fillable = [
        self::FIELD_CODE,
        self::FIELD_NAME,
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
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.'|unique:'. self::TABLE_NAME,
            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
            self::FIELD_NAME => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
        ];
    }

    /**
     * Relation to products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::BIND_NAME, Product::FIELD_ID_PRODUCT_TAX_TYPE, self::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }
}
