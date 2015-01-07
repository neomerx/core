<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Models\ProductImage as ProductImageModel;

class ProductImage
{
    use ImageTrait;

    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var ProductImageModel
     */
    private $productImageModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @param Model             $product
     * @param ProductImageModel $productImage
     * @param LanguageModel     $language
     */
    public function __construct(Model $product, ProductImageModel $productImage, LanguageModel $language)
    {
        $this->productModel      = $product;
        $this->productImageModel = $productImage;
        $this->languageModel     = $language;
    }

    /**
     * Read product images.
     *
     * @param Model $product
     *
     * @return Collection
     */
    public function showProductImages(Model $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $productImages = $product->productImages()
            ->with('image.properties.language', 'image.paths.format')
            ->get();

        return $productImages;
    }

    /**
     * Add product images.
     *
     * @param Model $product
     * @param array $descriptions
     * @param array $files
     *
     * @return void
     */
    public function storeProductImages(Model $product, array $descriptions, array $files)
    {
        Permissions::check($product, Permission::edit());
        $this->saveImages($descriptions, $files, $this->languageModel, $this->productImageModel, $product);
    }

    /**
     * Set product image as cover.
     *
     * @param Model $product
     * @param int   $imageId
     *
     * @return void
     */
    public function setDefaultProductImage(Model $product, $imageId)
    {
        Permissions::check($product, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var ProductImageModel $productImage */
        $productImage = $product->productImages()->where(ProductImageModel::FIELD_ID, '=', $imageId)->firstOrFail();
        $productImage->setAsCover();

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'defaultImageChanged', $product));
    }

    /**
     * Remove product images.
     *
     * @param Model $product
     * @param int   $imageId
     *
     * @return void
     */
    public function destroyProductImage(Model $product, $imageId)
    {
        Permissions::check($product, Permission::edit());

        // re-select them to check that they do belong to $product
        /** @noinspection PhpUndefinedMethodInspection */
        $image = $product->productImages()
            ->where(ProductImageModel::FIELD_ID, $imageId)->lists(ProductImageModel::FIELD_ID);

        $this->productImageModel->destroy($image);

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedImage', $product));
    }
}
