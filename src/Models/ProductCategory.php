<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_category_product
 * @property int      id_product
 * @property int      id_category
 * @property int      position
 * @property Product  product
 * @property Category category
 */
class ProductCategory extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'categories_products';

    const FIELD_ID          = 'id_category_product';
    const FIELD_ID_PRODUCT  = Product::FIELD_ID;
    const FIELD_ID_CATEGORY = Category::FIELD_ID;
    const FIELD_POSITION    = 'position';
    const FIELD_PRODUCT     = 'product';
    const FIELD_CATEGORY    = 'category';

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
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_CATEGORY,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_CATEGORY,
    ];

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::BIND_NAME, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::BIND_NAME, self::FIELD_ID_CATEGORY, Category::FIELD_ID);
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
