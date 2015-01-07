<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;

class VariantImage
{
    use ImageTrait;

    /**
     * @var array Used model relations.
     */
    protected static $relations = [
        'image.paths.format',
        'image.properties.language',
    ];

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @var ProductImage
     */
    private $productImageModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @param Variant      $variant
     * @param ProductImage $productImage
     * @param Language     $language
     */
    public function __construct(Variant $variant, ProductImage $productImage, Language $language)
    {
        $this->variantModel      = $variant;
        $this->productImageModel = $productImage;
        $this->languageModel     = $language;
    }

    /**
     * Read variant images.
     *
     * @param Variant $variant
     *
     * @return Collection
     */
    public function showVariantImages(Variant $variant)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $images = $variant->images()->with(static::$relations)->get();
        return $images;
    }

    /**
     * Add variant images.
     *
     * @param Variant $variant
     * @param array   $descriptions
     * @param array   $files
     *
     * @return void
     */
    public function storeVariantImages(Variant $variant, array $descriptions, array $files)
    {
        Permissions::check($variant->product, Permission::edit());

        $this->saveImages(
            $descriptions,
            $files,
            $this->languageModel,
            $this->productImageModel,
            $variant->product,
            $variant
        );
    }

    /**
     * Remove variant images.
     *
     * @param Variant $variant
     * @param int     $imageId
     *
     * @return void
     */
    public function destroyVariantImage(Variant $variant, $imageId)
    {
        Permissions::check($variant->product, Permission::edit());

        // re-select them to check that they do belong to $product
        /** @noinspection PhpUndefinedMethodInspection */
        $image = $variant->images()
            ->where(ProductImage::FIELD_ID, $imageId)->lists(ProductImage::FIELD_ID);

        $this->productImageModel->destroy($image);

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedImage', $variant));
    }
}
