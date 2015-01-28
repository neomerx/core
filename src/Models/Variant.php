<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_variant
 * @property      int        id_product
 * @property      string     sku
 * @property      float      price_wo_tax
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Product    product
 * @property      Collection properties
 * @property      Collection specification
 * @property      Collection images
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Variant extends BaseModel implements SelectByCodeInterface, GetSpecificationInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'variants';

    const SKU_MAX_LENGTH = Product::SKU_MAX_LENGTH;

    const FIELD_ID            = 'id_variant';
    const FIELD_ID_PRODUCT    = Product::FIELD_ID;
    const FIELD_SKU           = 'sku';
    const FIELD_PRICE_WO_TAX  = 'price_wo_tax';
    const FIELD_CREATED_AT    = 'created_at';
    const FIELD_UPDATED_AT    = 'updated_at';
    const FIELD_PRODUCT       = 'product';
    const FIELD_PROPERTIES    = 'properties';
    const FIELD_SPECIFICATION = 'specification';
    const FIELD_IMAGES        = 'images';

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
    protected $fillable = [
        self::FIELD_SKU,
        self::FIELD_PRICE_WO_TAX,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $touches = [
        self::FIELD_PRODUCT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT   => 'required|integer|min:1|max:4294967295',
            self::FIELD_SKU          => 'required|alpha_dash|min:1|max:'.self::SKU_MAX_LENGTH,
            self::FIELD_PRICE_WO_TAX => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT   => 'sometimes|required|forbidden',
            self::FIELD_SKU          => 'sometimes|required|forbidden',
            self::FIELD_PRICE_WO_TAX => 'sometimes|numeric|min:0',
        ];
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withTaxType()
    {
        return self::FIELD_PRODUCT.'.'.Product::FIELD_TAX_TYPE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withManufacturer()
    {
        return self::FIELD_PRODUCT.'.'.Product::FIELD_MANUFACTURER;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withDefaultCategory()
    {
        return self::FIELD_PRODUCT.'.'.Product::FIELD_DEFAULT_CATEGORY;
    }

    /**
     * Get variant price if specified otherwise return parent's product price.
     *
     * @param $value
     *
     * @return float
     */
    public function getPriceWoTaxAttribute($value)
    {
        return $value !== null ? $value : $this->product->price_wo_tax;
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::BIND_NAME, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification()
    {
        return $this->hasMany(Specification::BIND_NAME, Specification::FIELD_ID_VARIANT, self::FIELD_ID);
    }

    /**
     * Relation to images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::BIND_NAME, ProductImage::FIELD_ID_VARIANT, self::FIELD_ID);
    }

    /**
     * Relation to product language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(VariantProperties::BIND_NAME, VariantProperties::FIELD_ID_VARIANT, self::FIELD_ID);
    }

    /**
     * Check if variant is default for a product.
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->sku === $this->product->sku;
    }

    /**
     * Select default variant for product.
     *
     * @param Product $product
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function selectDefault(Product $product)
    {
        return static::query()->where(self::FIELD_SKU, '=', $product->sku);
    }

    /**
     * @param string $sku
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCode($sku)
    {
        return $this->newQuery()->where(self::FIELD_SKU, '=', $sku);
    }

    /**
     * @param array $variantSKUs
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCodes(array $variantSKUs)
    {
        $builder = $this->newQuery();
        $builder->getQuery()->whereIn(self::FIELD_SKU, $variantSKUs);
        return $builder;
    }

    /**
     * {@inheritdoc}
     *
     * If an ordinary non-default variant is deleted then no specific. Just delete it with its properties.
     * Default variant could not be 'deleted'. Use 'reset to default' instead.
     */
    protected function onDeleting()
    {
        $parentOnDeleting = parent::onDeleting();

        // only non-default variant could actually be deleted.
        $canBeDeleted = !$this->isDefault();

        return $parentOnDeleting and $canBeDeleted;
    }
}
