<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Exceptions\ValidationException;

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
 */
class ProductImage extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'product_images';

    const FIELD_ID         = 'id_product_image';
    const FIELD_ID_PRODUCT = Product::FIELD_ID;
    const FIELD_ID_VARIANT = Variant::FIELD_ID;
    const FIELD_ID_IMAGE   = Image::FIELD_ID;
    const FIELD_POSITION   = 'position';
    const FIELD_IS_COVER   = 'is_cover';
    const FIELD_IMAGE      = 'image';
    const FIELD_PRODUCT    = 'product';
    const FIELD_VARIANT    = 'variant';

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
    protected $fillable = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_IMAGE,
        self::FIELD_POSITION,
        self::FIELD_IS_COVER,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_VARIANT,
        self::FIELD_ID_IMAGE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'required|integer|min:1|max:4294967295|exists:' . Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|integer|min:1|max:4294967295|exists:' . Variant::TABLE_NAME,
            self::FIELD_ID_IMAGE   => 'required|integer|min:1|max:4294967295|exists:' . Image::TABLE_NAME,
            self::FIELD_POSITION   => 'required|numeric|min:0|max:255',
            self::FIELD_IS_COVER   => 'required|boolean',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Product::TABLE_NAME,
            self::FIELD_ID_VARIANT => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Variant::TABLE_NAME,
            self::FIELD_ID_IMAGE   => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Image::TABLE_NAME,
            self::FIELD_POSITION   => 'sometimes|required|numeric|min:0|max:255',
            self::FIELD_IS_COVER   => 'sometimes|required|boolean',
        ];
    }

    /**
     * Set is cover attribute.
     *
     * @param $value
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     */
    public function setIsCoverAttribute($value)
    {
        settype($value, 'boolean');
        $this->attributes[self::FIELD_IS_COVER] = $value;
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
        return $this->belongsTo(Product::BIND_NAME, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::BIND_NAME, self::FIELD_ID_IMAGE, Image::FIELD_ID);
    }

    /**
     * Relation to variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::BIND_NAME, self::FIELD_ID_VARIANT, Variant::FIELD_ID);
    }

    /**
     * {@inheritdoc}
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

    /**
     * Sets other product images of the same product as non-cover if current set as cover.
     *
     * @return bool
     */
    protected function onUpdated()
    {
        $onUpdated = parent::onUpdated();
        /** @noinspection PhpUndefinedFieldInspection */
        $isCoversToFalse = $onUpdated and $this->is_cover and $this->isDirty(self::FIELD_IS_COVER);
        $isCoversToFalse ? $this->setOtherProductImagesCoverToFalse() : null;
        return $onUpdated;
    }

    /**
     * @param Product $product
     * @param string  $fileName
     * @param array   $description
     * @param array   $imageProperties
     * @param Variant $variant
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addImageOrFail(
        Product $product,
        $fileName,
        array $description,
        array $imageProperties,
        Variant $variant = null
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // create image
            $image = Image::create(['original_file' => $fileName]);
            $image->exists ?: S\throwEx(new LogicException());

            // create product image
            $productImageLinks = [
                self::FIELD_ID_PRODUCT => $product->{Product::FIELD_ID},
                self::FIELD_ID_VARIANT => $variant ? $variant->{Variant::FIELD_ID} : null,
                self::FIELD_ID_IMAGE   => $image->{Image::FIELD_ID},
            ];
            // if we store a variant image it can't be a product cover image
            if ($variant !== null and isset($description[self::FIELD_IS_COVER])) {
                $description[self::FIELD_IS_COVER] = false;
            }
            $productImage = self::create(array_merge($description, $productImageLinks));
            /** @noinspection PhpUndefinedMethodInspection */
            $productImage->exists ?: S\throwEx(new ValidationException($productImage->getValidator()));

            // if the image is cover reinforce it (set others as false and this one as true).
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedFieldInspection */
            $productImage->is_cover ? $productImage->setAsCover() : null;

            // create image properties
            $imageId = $image->{Image::FIELD_ID};
            foreach ($imageProperties as $imagePropertyData) {
                $imageProperty = ImageProperties::create(array_merge(
                    $imagePropertyData,
                    [self::FIELD_ID_IMAGE => $imageId]
                ));
                /** @noinspection PhpUndefinedMethodInspection */
                $imageProperty->exists ?: S\throwEx(new ValidationException($imageProperty->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * Set product image as cover.
     */
    public function setAsCover()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setOtherProductImagesCoverToFalse();
            $this->updateOrFail([self::FIELD_IS_COVER => true]);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    private function setOtherProductImagesCoverToFalse()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->product->productImages()->where(self::FIELD_ID, '<>', $this->{self::FIELD_ID})
            ->update([self::FIELD_IS_COVER => false]);
    }
}
