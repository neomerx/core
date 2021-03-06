<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_category_product
 * @property int      id_product
 * @property int      id_category
 * @property int      position
 * @property Product  product
 * @property Category category
 *
 * @package Neomerx\Core
 */
class ProductCategory extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'categories_products';

    /** Model field name */
    const FIELD_ID          = 'id_category_product';
    /** Model field name */
    const FIELD_ID_PRODUCT  = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_CATEGORY = Category::FIELD_ID;
    /** Model field name */
    const FIELD_POSITION    = 'position';
    /** Model field name */
    const FIELD_PRODUCT     = 'product';
    /** Model field name */
    const FIELD_CATEGORY    = 'category';

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
        self::FIELD_POSITION,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_CATEGORY,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_CATEGORY,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT  => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_CATEGORY => 'required|integer|min:1|max:4294967295|exists:'.Category::TABLE_NAME,
            self::FIELD_POSITION    => 'required|integer|min:0|max:65535',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_CATEGORY => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Category::TABLE_NAME,
            self::FIELD_POSITION    => 'sometimes|required|integer|min:0|max:65535',
        ];
    }

    /**
     * @return string
     */
    public static function withProduct()
    {
        return self::FIELD_PRODUCT;
    }

    /**
     * @return string
     */
    public static function withCategory()
    {
        return self::FIELD_CATEGORY;
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, self::FIELD_ID_CATEGORY, Category::FIELD_ID);
    }

    /**
     * Select max/last product position in category.
     *
     * @param int $categoryId
     *
     * @return mixed
     */
    public function selectMaxPosition($categoryId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->newQuery()->where(self::FIELD_ID_CATEGORY, '=', $categoryId)->max(self::FIELD_POSITION);
    }
}
