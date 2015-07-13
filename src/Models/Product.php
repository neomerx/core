<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int            id_product
 * @property      int            id_base_product
 * @property      int            id_category_default
 * @property      int            id_product_tax_type
 * @property      string         sku
 * @property      float          price_wo_tax
 * @property      Category       defaultCategory
 * @property      ProductTaxType taxType
 * @property-read Carbon         created_at
 * @property-read Carbon         updated_at
 * @property      BaseProduct    baseProduct
 * @property      Collection     properties
 * @property      Collection     assignedCategories
 * @property      Collection     productCategories
 * @property      Collection     related
 * @property      Collection     relatedProducts
 * @property      Collection     specification
 * @property      Collection     images
 * @property      Collection     inventories
 *
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends BaseModel implements SelectByCodeInterface, GetSpecificationInterface
{
    /** Model table name */
    const TABLE_NAME = 'products';

    /** Model field length */
    const SKU_MAX_LENGTH = BaseProduct::SKU_MAX_LENGTH;

    /** Model field name */
    const FIELD_ID                  = 'id_product';
    /** Model field name */
    const FIELD_ID_BASE_PRODUCT     = BaseProduct::FIELD_ID;
    /** Model field name */
    const FIELD_SKU                 = 'sku';
    /** Model field name */
    const FIELD_PRICE_WO_TAX        = 'price_wo_tax';
    /** Model field name */
    const FIELD_ID_CATEGORY_DEFAULT = 'id_category_default';
    /** Model field name */
    const FIELD_ID_PRODUCT_TAX_TYPE = ProductTaxType::FIELD_ID;
    /** Model field name */
    const FIELD_CREATED_AT          = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT          = 'updated_at';
    /** Model field name */
    const FIELD_BASE_PRODUCT        = 'baseProduct';
    /** Model field name */
    const FIELD_PROPERTIES          = 'properties';
    /** Model field name */
    const FIELD_ASSIGNED_CATEGORIES = 'assignedCategories';
    /** Model field name */
    const FIELD_PRODUCT_CATEGORIES  = 'productCategories';
    /** Model field name */
    const FIELD_RELATED_PRODUCTS    = 'relatedProducts';
    /** Model field name */
    const FIELD_SPECIFICATION       = 'specification';
    /** Model field name */
    const FIELD_IMAGES              = 'images';
    /** Model field name */
    const FIELD_DEFAULT_CATEGORY    = 'defaultCategory';
    /** Model field name */
    const FIELD_TAX_TYPE            = 'taxType';

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
        self::FIELD_SKU,
        self::FIELD_PRICE_WO_TAX,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_BASE_PRODUCT,
        self::FIELD_ID_CATEGORY_DEFAULT,
        self::FIELD_ID_PRODUCT_TAX_TYPE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_BASE_PRODUCT,
        self::FIELD_ID_CATEGORY_DEFAULT,
        self::FIELD_ID_PRODUCT_TAX_TYPE,
    ];

    /**
     * @inheritdoc
     */
    protected $touches = [
        self::FIELD_BASE_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_BASE_PRODUCT => 'required|integer|min:1|max:4294967295',
            self::FIELD_SKU             => 'required|alpha_dash|min:1|max:'.self::SKU_MAX_LENGTH,
            self::FIELD_PRICE_WO_TAX    => 'sometimes|required|numeric|min:0',

            self::FIELD_ID_CATEGORY_DEFAULT => 'required|integer|min:1|max:4294967295|exists:'.
                Category::TABLE_NAME.','.Category::FIELD_ID,

            self::FIELD_ID_PRODUCT_TAX_TYPE => 'required|integer|min:1|max:4294967295|exists:'.
                ProductTaxType::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_BASE_PRODUCT => 'sometimes|required|forbidden',
            self::FIELD_SKU             => 'sometimes|required|forbidden',
            self::FIELD_PRICE_WO_TAX    => 'sometimes|numeric|min:0',

            self::FIELD_ID_CATEGORY_DEFAULT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Category::TABLE_NAME.','.Category::FIELD_ID,

            self::FIELD_ID_PRODUCT_TAX_TYPE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ProductTaxType::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.ProductProperties::FIELD_LANGUAGE;
    }

    /**
     * @return string
     */
    public static function withDefaultCategory()
    {
        return self::FIELD_DEFAULT_CATEGORY;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withTaxType()
    {
        return self::FIELD_TAX_TYPE;
    }

    /**
     * Get model relation.
     *
     * @return string
     */
    public static function withManufacturer()
    {
        return self::FIELD_BASE_PRODUCT.'.'.BaseProduct::FIELD_MANUFACTURER;
    }

    /**
     * Relation to categories directly.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedCategories()
    {
        return $this
            ->belongsToMany(
                Category::class,
                ProductCategory::TABLE_NAME,
                ProductCategory::FIELD_ID_PRODUCT,
                ProductCategory::FIELD_ID_CATEGORY
            )->withPivot(ProductCategory::FIELD_POSITION);
    }

    /**
     * Relation to product categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, ProductCategory::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to 'related products'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function related()
    {
        return $this->hasMany(ProductRelated::class, ProductRelated::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Direct relation to related products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function relatedProducts()
    {
        return $this->belongsToMany(
            self::class,
            ProductRelated::TABLE_NAME,
            ProductRelated::FIELD_ID_PRODUCT,
            ProductRelated::FIELD_ID_RELATED_PRODUCT
        );
    }

    /**
     * Get product price if specified otherwise return parent's product price.
     *
     * @param $value
     *
     * @return float
     */
    public function getPriceWoTaxAttribute($value)
    {
        return $value !== null ? $value : $this->{self::FIELD_BASE_PRODUCT}->price_wo_tax;
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function baseProduct()
    {
        return $this->belongsTo(BaseProduct::class, self::FIELD_ID_BASE_PRODUCT, BaseProduct::FIELD_ID);
    }

    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification()
    {
        return $this->hasMany(Specification::class, Specification::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, ProductImage::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to inventories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, Inventory::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to default category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultCategory()
    {
        return $this->hasOne(Category::class, Category::FIELD_ID, self::FIELD_ID_CATEGORY_DEFAULT);
    }

    /**
     * Relation to tax type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxType()
    {
        return $this->belongsTo(ProductTaxType::class, self::FIELD_ID_PRODUCT_TAX_TYPE, ProductTaxType::FIELD_ID);
    }

    /**
     * Relation to product language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(ProductProperties::class, ProductProperties::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Check if product is default.
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->{self::FIELD_SKU} === $this->{self::FIELD_BASE_PRODUCT}->{BaseProduct::FIELD_SKU};
    }

    /**
     * Select default product for base product.
     *
     * @param BaseProduct $base
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function selectDefault(BaseProduct $base)
    {
        return static::query()->where(self::FIELD_SKU, '=', $base->{BaseProduct::FIELD_SKU});
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
     * @param array $productSKUs
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCodes(array $productSKUs)
    {
        $builder = $this->newQuery();
        $builder->getQuery()->whereIn(self::FIELD_SKU, $productSKUs);
        return $builder;
    }

    /**
     * @inheritdoc
     *
     * If an ordinary non-default product is deleted then no specific. Just delete it with its properties.
     * Default product could not be 'deleted'. Use 'reset to default' instead.
     */
    protected function onDeleting()
    {
        $parentOnDeleting = parent::onDeleting();

        // only non-default product could actually be deleted.
        $canBeDeleted = !$this->isDefault();

        return $parentOnDeleting && $canBeDeleted;
    }
}
