<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;

/**
 * @property      int            id_product
 * @property      int            id_category_default
 * @property      int            id_manufacturer
 * @property      int            id_product_tax_type
 * @property      string         sku
 * @property      string         link
 * @property      float          price_wo_tax
 * @property      float          pkg_height
 * @property      float          pkg_width
 * @property      float          pkg_length
 * @property      float          pkg_weight
 * @property      bool           enabled
 * @property-read Carbon         created_at
 * @property-read Carbon         updated_at
 * @property      Category       default_category
 * @property      Manufacturer   manufacturer
 * @property      ProductTaxType tax_type
 * @property      Collection     assigned_categories
 * @property      Collection     product_categories
 * @property      Collection     related
 * @property      Collection     related_products
 * @property      Collection     properties
 * @property      Collection     specification
 * @property      Collection     product_images
 * @property      Collection     variants
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends BaseModel implements SelectByCodeInterface, GetSpecificationInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'products';

    const SKU_MAX_LENGTH  = 64;
    const LINK_MAX_LENGTH = 50;

    const FIELD_ID                  = 'id_product';
    const FIELD_ID_CATEGORY_DEFAULT = 'id_category_default';
    const FIELD_ID_MANUFACTURER     = Manufacturer::FIELD_ID;
    const FIELD_ID_PRODUCT_TAX_TYPE = ProductTaxType::FIELD_ID;
    const FIELD_SKU                 = 'sku';
    const FIELD_LINK                = 'link';
    const FIELD_PRICE_WO_TAX        = 'price_wo_tax';
    const FIELD_ENABLED             = 'enabled';
    const FIELD_CREATED_AT          = 'created_at';
    const FIELD_UPDATED_AT          = 'updated_at';
    const FIELD_TAX_TYPE            = 'tax_type';
    const FIELD_DEFAULT_CATEGORY    = 'default_category';
    const FIELD_ASSIGNED_CATEGORIES = 'assigned_categories';
    const FIELD_PRODUCT_CATEGORIES  = 'product_categories';
    const FIELD_RELATED_PRODUCTS    = 'related_products';
    const FIELD_PRODUCT_IMAGES      = 'product_images';
    const FIELD_RELATED             = 'related';
    const FIELD_MANUFACTURER        = 'manufacturer';
    const FIELD_PROPERTIES          = 'properties';
    const FIELD_SPECIFICATION       = 'specification';
    const FIELD_IMAGES              = 'images';
    const FIELD_VARIANTS            = 'variants';
    const FIELD_PKG_HEIGHT          = 'pkg_height';
    const FIELD_PKG_WIDTH           = 'pkg_width';
    const FIELD_PKG_LENGTH          = 'pkg_length';
    const FIELD_PKG_WEIGHT          = 'pkg_weight';

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
        self::FIELD_ID_CATEGORY_DEFAULT,
        self::FIELD_LINK,
        self::FIELD_ID_MANUFACTURER,
        self::FIELD_ID_PRODUCT_TAX_TYPE,
        self::FIELD_ENABLED,
        self::FIELD_PRICE_WO_TAX,
        self::FIELD_PKG_HEIGHT,
        self::FIELD_PKG_WIDTH,
        self::FIELD_PKG_LENGTH,
        self::FIELD_PKG_WEIGHT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_CATEGORY_DEFAULT,
        self::FIELD_ID_MANUFACTURER,
        self::FIELD_ID_PRODUCT_TAX_TYPE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_SKU => 'required|alpha_dash|min:1|max:' . self::SKU_MAX_LENGTH . '|unique:' . self::TABLE_NAME,

            self::FIELD_ID_CATEGORY_DEFAULT => 'required|integer|min:1|max:4294967295|exists:' .
                Category::TABLE_NAME . ',' . Category::FIELD_ID,

            self::FIELD_LINK => 'required|alpha_dash|min:1|max:' . self::LINK_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,

            self::FIELD_ID_MANUFACTURER => 'required|integer|min:1|max:4294967295|exists:' . Manufacturer::TABLE_NAME,

            self::FIELD_ID_PRODUCT_TAX_TYPE => 'required|integer|min:1|max:4294967295|exists:' .
                ProductTaxType::TABLE_NAME,

            self::FIELD_ENABLED      => 'required|boolean',
            self::FIELD_PRICE_WO_TAX => 'required|numeric|min:0',
            self::FIELD_PKG_HEIGHT   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WIDTH    => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_LENGTH   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WEIGHT   => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_SKU  => 'sometimes|required|forbidden',

            self::FIELD_ID_CATEGORY_DEFAULT => 'sometimes|required|integer|min:1|max:4294967295|exists:' .
                Category::TABLE_NAME . ',' . Category::FIELD_ID,

            self::FIELD_LINK => 'sometimes|required|alpha_dash|min:1|max:' . self::LINK_MAX_LENGTH .
                '|unique:'. self::TABLE_NAME,

            self::FIELD_ID_MANUFACTURER =>'sometimes|required|integer|min:1|max:4294967295|exists:' .
                Manufacturer::TABLE_NAME,

            self::FIELD_ID_PRODUCT_TAX_TYPE  =>'sometimes|required|integer|min:1|max:4294967295|exists:' .
                ProductTaxType::TABLE_NAME,

            self::FIELD_ENABLED      => 'sometimes|required|boolean',
            self::FIELD_PRICE_WO_TAX => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_HEIGHT   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WIDTH    => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_LENGTH   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WEIGHT   => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * Relation to default category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultCategory()
    {
        return $this->hasOne(Category::BIND_NAME, Category::FIELD_ID, self::FIELD_ID_CATEGORY_DEFAULT);
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
                Category::BIND_NAME,
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
        return $this->hasMany(ProductCategory::BIND_NAME, ProductCategory::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to 'related products'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function related()
    {
        return $this->hasMany(ProductRelated::BIND_NAME, ProductRelated::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Direct relation to related products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::BIND_NAME,
            ProductRelated::TABLE_NAME,
            ProductRelated::FIELD_ID_PRODUCT,
            ProductRelated::FIELD_ID_RELATED_PRODUCT
        );
    }

    /**
     * Relation to product language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(ProductProperties::BIND_NAME, ProductProperties::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(Specification::BIND_NAME, Specification::FIELD_ID_PRODUCT, self::FIELD_ID)
            ->whereNull(Variant::FIELD_ID);
    }

    /**
     * Relation to images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productImages()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(ProductImage::BIND_NAME, ProductImage::FIELD_ID_PRODUCT, self::FIELD_ID)
            ->whereNull(Variant::FIELD_ID);
    }

    /**
     * Relation to manufacturer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::BIND_NAME, self::FIELD_ID_MANUFACTURER, Manufacturer::FIELD_ID);
    }

    /**
     * Relation to tax type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxType()
    {
        return $this->belongsTo(ProductTaxType::BIND_NAME, self::FIELD_ID_PRODUCT_TAX_TYPE, ProductTaxType::FIELD_ID);
    }

    /**
     * Relation to product variants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->hasMany(Variant::BIND_NAME, Variant::FIELD_ID_PRODUCT, self::FIELD_ID);
    }

    /**
     * Create default variant on product creation.
     *
     * @return bool
     */
    protected function onCreated()
    {
        $productCreated = parent::onCreated();

        /** @var Variant $variant */
        /** @noinspection PhpUndefinedMethodInspection */
        $variant = App::make(Variant::BIND_NAME);
        $productCreated ?
            $variant->createOrFail([
                Variant::FIELD_ID_PRODUCT => $this->{self::FIELD_ID},
                Variant::FIELD_SKU        => $this->{self::FIELD_SKU},
            ]) : null;

        return $productCreated;
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
     * Create a variant of product with specification identical to default product variant.
     *
     * @param array $attributes
     *
     * @return Variant
     */
    public function addVariant(array $attributes)
    {
        /** @var Variant $variant */
        /** @noinspection PhpUndefinedMethodInspection */
        $variant = App::make(Variant::BIND_NAME);

        // select specifications from default product variant
        /** @var Variant $defaultVariant */
        $defaultVariant = $variant->selectDefault($this)->firstOrFail();
        $defaultSpecs   = $defaultVariant->specification;

        $variant->fill($attributes);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // save variant and copy specification from default variant
            $this->variants()->save($variant) ?:S\throwEx(new ValidationException($variant->getValidator()));

            foreach ($defaultSpecs as $defaultSpec) {
                /** @var Specification $defaultSpec */
                $specData = $defaultSpec->attributesToArray();
                $specData[Specification::FIELD_ID_PRODUCT]              = $defaultSpec->{Product::FIELD_ID};
                $specData[Specification::FIELD_ID_CHARACTERISTIC_VALUE] = $defaultSpec->{CharacteristicValue::FIELD_ID};
                $specData[Specification::FIELD_ID_VARIANT]              = $variant->{Variant::FIELD_ID};

                /** @var Specification $spec */
                /** @noinspection PhpUndefinedMethodInspection */
                $spec = App::make(Specification::BIND_NAME);
                $spec->createOrFail($specData);
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        return $variant;
    }
}
