<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;

class ProductImages
{
    use ImageTrait;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var ProductImage
     */
    private $productImageModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @param Product             $product
     * @param ProductImage $productImage
     * @param Language     $language
     */
    public function __construct(Product $product, ProductImage $productImage, Language $language)
    {
        $this->productModel      = $product;
        $this->productImageModel = $productImage;
        $this->languageModel     = $language;
    }

    /**
     * Read product images.
     *
     * @param Product $product
     *
     * @return Collection
     */
    public function showProductImages(Product $product)
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
     * @param Product $product
     * @param array $descriptions
     * @param array $files
     *
     * @return void
     */
    public function storeProductImages(Product $product, array $descriptions, array $files)
    {
        Permissions::check($product, Permission::edit());
        $this->saveImages($descriptions, $files, $this->languageModel, $this->productImageModel, $product);
    }

    /**
     * Set product image as cover.
     *
     * @param Product $product
     * @param int   $imageId
     *
     * @return void
     */
    public function setDefaultProductImage(Product $product, $imageId)
    {
        Permissions::check($product, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var ProductImage $productImage */
        $productImage = $product->productImages()->where(ProductImage::FIELD_ID, '=', $imageId)->firstOrFail();
        $productImage->setAsCover();

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'defaultImageChanged', $product));
    }

    /**
     * Remove product images.
     *
     * @param Product $product
     * @param int   $imageId
     *
     * @return void
     */
    public function destroyProductImage(Product $product, $imageId)
    {
        Permissions::check($product, Permission::edit());

        // re-select them to check that they do belong to $product
        /** @noinspection PhpUndefinedMethodInspection */
        $image = $product->productImages()
            ->where(ProductImage::FIELD_ID, $imageId)->lists(ProductImage::FIELD_ID);

        $this->productImageModel->destroy($image);

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedImage', $product));
    }
}
