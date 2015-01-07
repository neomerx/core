<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Models\ProductImage as ProductImageModel;

class VariantImage
{
    use ImageTrait;

    /**
     * @var array Used model relations.
     */
    private static $relations = [
        'image.paths.format',
        'image.properties.language',
    ];

    /**
     * @var VariantModel
     */
    private $variantModel;

    /**
     * @var ProductImageModel
     */
    private $productImageModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @param VariantModel      $variant
     * @param ProductImageModel $productImage
     * @param LanguageModel     $language
     */
    public function __construct(VariantModel $variant, ProductImageModel $productImage, LanguageModel $language)
    {
        $this->variantModel      = $variant;
        $this->productImageModel = $productImage;
        $this->languageModel     = $language;
    }

    /**
     * Read variant images.
     *
     * @param VariantModel $variant
     *
     * @return Collection
     */
    public function showVariantImages(VariantModel $variant)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $images = $variant->images()->with(self::$relations)->get();
        return $images;
    }

    /**
     * Add variant images.
     *
     * @param VariantModel $variant
     * @param array        $descriptions
     * @param array        $files
     *
     * @return void
     */
    public function storeVariantImages(VariantModel $variant, array $descriptions, array $files)
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
     * @param VariantModel $variant
     * @param int          $imageId
     *
     * @return void
     */
    public function destroyVariantImage(VariantModel $variant, $imageId)
    {
        Permissions::check($variant->product, Permission::edit());

        // re-select them to check that they do belong to $product
        /** @noinspection PhpUndefinedMethodInspection */
        $image = $variant->images()
            ->where(ProductImageModel::FIELD_ID, $imageId)->lists(ProductImageModel::FIELD_ID);

        $this->productImageModel->destroy($image);

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedImage', $variant));
    }
}
