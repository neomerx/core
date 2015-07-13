<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int            id_base_product
 * @property      int            id_manufacturer
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
 * @property      Manufacturer   manufacturer
 * @property      Collection     properties
 * @property      Collection     specification
 * @property      Collection     productImages
 * @property      Collection     products
 *
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BaseProduct extends BaseModel implements SelectByCodeInterface, GetSpecificationInterface
{
    /** Model table name */
    const TABLE_NAME = 'base_products';

    /** Model field length */
    const SKU_MAX_LENGTH  = 64;
    /** Model field length */
    const LINK_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID                  = 'id_base_product';
    /** Model field name */
    const FIELD_ID_MANUFACTURER     = Manufacturer::FIELD_ID;
    /** Model field name */
    const FIELD_SKU                 = 'sku';
    /** Model field name */
    const FIELD_LINK                = 'link';
    /** Model field name */
    const FIELD_PRICE_WO_TAX        = 'price_wo_tax';
    /** Model field name */
    const FIELD_ENABLED             = 'enabled';
    /** Model field name */
    const FIELD_CREATED_AT          = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT          = 'updated_at';
    /** Model field name */
    const FIELD_PRODUCT_IMAGES      = 'productImages';
    /** Model field name */
    const FIELD_RELATED             = 'related';
    /** Model field name */
    const FIELD_MANUFACTURER        = 'manufacturer';
    /** Model field name */
    const FIELD_PROPERTIES          = 'properties';
    /** Model field name */
    const FIELD_SPECIFICATION       = 'specification';
    /** Model field name */
    const FIELD_IMAGES              = 'images';
    /** Model field name */
    const FIELD_PRODUCTS            = 'products';
    /** Model field name */
    const FIELD_PKG_HEIGHT          = 'pkg_height';
    /** Model field name */
    const FIELD_PKG_WIDTH           = 'pkg_width';
    /** Model field name */
    const FIELD_PKG_LENGTH          = 'pkg_length';
    /** Model field name */
    const FIELD_PKG_WEIGHT          = 'pkg_weight';

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
        self::FIELD_LINK,
        self::FIELD_ENABLED,
        self::FIELD_PRICE_WO_TAX,
        self::FIELD_PKG_WEIGHT,
        self::FIELD_PKG_LENGTH,
        self::FIELD_PKG_WIDTH,
        self::FIELD_PKG_HEIGHT,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_MANUFACTURER,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_MANUFACTURER,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_SKU => 'required|alpha_dash|min:1|max:'.self::SKU_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_LINK => 'required|alpha_dash|min:1|max:'.self::LINK_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_ID_MANUFACTURER => 'required|integer|min:1|max:4294967295|exists:'.Manufacturer::TABLE_NAME,

            self::FIELD_ENABLED      => 'required|boolean',
            self::FIELD_PRICE_WO_TAX => 'required|numeric|min:0',
            self::FIELD_PKG_HEIGHT   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WIDTH    => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_LENGTH   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WEIGHT   => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_SKU  => 'sometimes|required|forbidden',

            self::FIELD_LINK => 'sometimes|required|alpha_dash|min:1|max:'.self::LINK_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,

            self::FIELD_ID_MANUFACTURER => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                Manufacturer::TABLE_NAME,

            self::FIELD_ENABLED      => 'sometimes|required|boolean',
            self::FIELD_PRICE_WO_TAX => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_HEIGHT   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WIDTH    => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_LENGTH   => 'sometimes|required|numeric|min:0',
            self::FIELD_PKG_WEIGHT   => 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.BaseProductProperties::FIELD_LANGUAGE;
    }

    /**
     * @return string
     */
    public static function withManufacturer()
    {
        return self::FIELD_MANUFACTURER;
    }

    /**
     * Relation to product language properties (translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(
            BaseProductProperties::class,
            BaseProductProperties::FIELD_ID_BASE_PRODUCT,
            self::FIELD_ID
        );
    }

    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(Specification::class, Specification::FIELD_ID_BASE_PRODUCT, self::FIELD_ID)
            ->whereNull(Product::FIELD_ID);
    }

    /**
     * Relation to images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productImages()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany(ProductImage::class, ProductImage::FIELD_ID_BASE_PRODUCT, self::FIELD_ID)
            ->whereNull(Product::FIELD_ID);
    }

    /**
     * Relation to manufacturer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, self::FIELD_ID_MANUFACTURER, Manufacturer::FIELD_ID);
    }

    /**
     * Relation to products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, Product::FIELD_ID_BASE_PRODUCT, self::FIELD_ID);
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
     * @param array $baseSKUs
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCodes(array $baseSKUs)
    {
        $builder = $this->newQuery();
        $builder->getQuery()->whereIn(self::FIELD_SKU, $baseSKUs);
        return $builder;
    }

    /**
     * Get default product.
     *
     * @return Product|null
     */
    public function getDefaultProduct()
    {
        $defaultProduct = null;

        foreach ($this->{self::FIELD_PRODUCTS} as $product) {
            /** @var Product $product */
            if ($product->isDefault() === true) {
                $defaultProduct = $product;
                break;
            }
        }

        return $defaultProduct;
    }
}
