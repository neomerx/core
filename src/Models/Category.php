<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @property      int        id_category
 * @property      int        id_ancestor
 * @property      string     code
 * @property      string     link
 * @property      bool       enabled
 * @property      int        lft
 * @property      int        rgt
 * @property-read int        number_of_descendants
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Category   ancestor
 * @property      Collection properties
 * @property      Collection products
 * @property      Collection assignedProducts
 * @property      Collection productCategories
 *
 * @package Neomerx\Core
 */
class Category extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'categories';

    /** Root category code */
    const ROOT_CODE       = '-';
    /** Model field length */
    const CODE_MAX_LENGTH = 50;
    /** Model field length */
    const LINK_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID                    = 'id_category';
    /** Model field name */
    const FIELD_CODE                  = 'code';
    /** Model field name */
    const FIELD_LINK                  = 'link';
    /** Model field name */
    const FIELD_LFT                   = 'lft';
    /** Model field name */
    const FIELD_RGT                   = 'rgt';
    /** Model field name */
    const FIELD_ENABLED               = 'enabled';
    /** Model field name */
    const FIELD_ID_ANCESTOR           = 'id_ancestor';
    /** Model field name */
    const FIELD_NUMBER_OF_DESCENDANTS = 'number_of_descendants';
    /** Model field name */
    const FIELD_ANCESTOR              = 'ancestor';
    /** Model field name */
    const FIELD_PROPERTIES            = 'properties';
    /** Model field name */
    const FIELD_PRODUCTS              = 'products';
    /** Model field name */
    const FIELD_ASSIGNED_PRODUCTS     = 'assignedProducts';
    /** Model field name */
    const FIELD_PRODUCT_CATEGORIES    = 'productCategories';
    /** Model field name */
    const FIELD_CREATED_AT            = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT            = 'updated_at';

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
        self::FIELD_CODE,
        self::FIELD_LINK,
        self::FIELD_ENABLED,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_ANCESTOR,
        self::FIELD_LFT,
        self::FIELD_RGT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ANCESTOR,
        self::FIELD_LFT,
        self::FIELD_RGT,
    ];

    /**
     * @inheritdoc
     */
    protected $appends = [
        self::FIELD_NUMBER_OF_DESCENDANTS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ANCESTOR   => 'required|integer|min:1|max:4294967295|exists:'.
                self::TABLE_NAME.','.self::FIELD_ID,

            self::FIELD_CODE => 'required|code|min:1|max:'.
                self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_LINK => 'required|min:1|max:'.self::LINK_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ENABLED => 'required|boolean',

            self::FIELD_LFT  => 'required|integer|min:0|max:4294967295|different:'.self::FIELD_RGT.'|unique:'.
                self::TABLE_NAME.','.self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_RGT,

            self::FIELD_RGT  => 'required|integer|min:0|max:4294967295|different:'.self::FIELD_LFT.'|unique:'.
                self::TABLE_NAME.','.self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_RGT,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ANCESTOR   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                self::TABLE_NAME.','.self::FIELD_ID,

            self::FIELD_CODE => 'sometimes|required|forbidden',

            self::FIELD_LINK => 'sometimes|required|min:1|max:'.self::LINK_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ENABLED => 'sometimes|required|boolean',

            self::FIELD_LFT => 'required_with:'.self::FIELD_RGT.'|integer|min:0|max:4294967295|different:'.
                self::FIELD_RGT.'|unique:'.self::TABLE_NAME.','.self::FIELD_LFT.'|'.'unique:'.
                self::TABLE_NAME.','.self::FIELD_RGT,

            self::FIELD_RGT => 'required_with:'.self::FIELD_LFT.'|integer|min:0|max:4294967295|different:'.
                self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_LFT.'|'.'unique:'.
                self::TABLE_NAME.','.self::FIELD_RGT,
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CategoryProperty::FIELD_LANGUAGE;
    }

    /**
     * @return integer
     */
    public function getNumberOfDescendantsAttribute()
    {
        return (int)(($this->rgt - $this->lft - 1) / 2);
    }

    /**
     * Relation to categories language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CategoryProperty::class, CategoryProperty::FIELD_ID_CATEGORY, self::FIELD_ID);
    }

    /**
     * Relation to products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            ProductCategory::TABLE_NAME,
            ProductCategory::FIELD_ID_CATEGORY,
            ProductCategory::FIELD_ID_PRODUCT
        )->withPivot(ProductCategory::FIELD_POSITION);
    }

    /**
     * Relation to product categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, ProductCategory::FIELD_ID_CATEGORY, self::FIELD_ID);
    }

    /**
     * Relation to ancestor category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ancestor()
    {
        return $this->hasOne(self::class, self::FIELD_ID, self::FIELD_ID_ANCESTOR);
    }
    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEnabledAttribute($value)
    {
        return (bool)$value;
    }
}
