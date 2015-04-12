<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @property int     id_product_image
 * @property int     id_product
 * @property int     id_variant
 * @property int     id_image
 * @property int     position
 * @property bool    is_cover
 * @property Image   image
 * @property Product product
 * @property Variant variant
 *
 * @package Neomerx\Core
 */
class ProductImage extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'product_images';

    /** Model field name */
    const FIELD_ID         = 'id_product_image';
    /** Model field name */
    const FIELD_ID_PRODUCT = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_VARIANT = Variant::FIELD_ID;
    /** Model field name */
    const FIELD_ID_IMAGE   = Image::FIELD_ID;
    /** Model field name */
    const FIELD_POSITION   = 'position';
    /** Model field name */
    const FIELD_IS_COVER   = 'is_cover';
    /** Model field name */
    const FIELD_IMAGE      = 'image';
    /** Model field name */
    const FIELD_PRODUCT    = 'product';
    /** Model field name */
    const FIELD_VARIANT    = 'variant';

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
        self::FIELD_ID_VARIANT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_IMAGE,
        self::FIELD_IS_COVER,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_ID_IMAGE   => 'required|integer|min:1|max:4294967295|exists:'.Image::TABLE_NAME,
            self::FIELD_POSITION   => 'required|numeric|min:0|max:255',

            // direct change  of 'is cover' is forbidden use repository's method instead
            self::FIELD_IS_COVER   => 'sometimes|required|forbidden',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'sometimes|required|forbidden',
            self::FIELD_ID_VARIANT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Variant::TABLE_NAME,
            self::FIELD_ID_IMAGE   => 'sometimes|required|forbidden',
            self::FIELD_POSITION   => 'sometimes|required|numeric|min:0|max:255',

            // direct change  of 'is cover' is forbidden use repository's method instead
            self::FIELD_IS_COVER   => 'sometimes|required|forbidden',
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
    public static function withVariant()
    {
        return self::FIELD_VARIANT;
    }

    /**
     * @return string
     */
    public static function withImage()
    {
        return self::FIELD_IMAGE;
    }

    /**
     * Set is cover attribute. Direct change of 'is cover' is forbidden. Use repository's method instead.
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     */
    public function setIsCoverAttribute()
    {
        // direct change  of 'is cover' is forbidden use repository's method instead
        throw new InvalidArgumentException('value');
    }

    /**
     * Get is cover attribute.
     *
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCoverAttribute($value)
    {
        return (bool)$value;
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
     * Relation to image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::class, self::FIELD_ID_IMAGE, Image::FIELD_ID);
    }

    /**
     * Relation to variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }

    /**
     * @inheritdoc
     *
     * Under normal conditions delete should work fine. However if deletion of the Image class instance failed
     * we will have inconsistency: product image would be removed but image object with its files would exist.
     * It's not a big problem as it only affects space on storage and in the database however needs to be investigated.
     * That's why we throw an exception.
     *
     * If this situation occurs it's necessary to find out the reason and fix either permission settings or
     * a bug in the code. Remaining images could be removed manually/automatically.
     */
    public function onDeleted()
    {
        $image = $this->image;
        $image->deleteOrFail();
    }
}
