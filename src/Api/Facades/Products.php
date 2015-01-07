<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Products\ProductsInterface;

/**
 * @see ProductsInterface
 *
 * @method static Product    create(array $input)
 * @method static Product    read(string $sku)
 * @method static void       update(string $sku, array $input)
 * @method static void       delete(string $sku)
 * @method static Collection search(array $parameters = [])
 * @method static void       updateRelated(Product $product, array $productSKUs)
 * @method static void       updateProductSpecification(Product $product, array $parameters = [])
 * @method static void       storeVariant(Product $product, array $input)
 * @method static array      showVariantImages(Variant $variant)
 * @method static void       updateVariantSpecification(Variant $variant, array $parameters = [])
 * @method static array      showRelated(Product $product)
 * @method static void       updateVariant(Variant $variant, array $input)
 * @method static void       destroyVariantImage(Variant $variant, int $imageId)
 * @method static void       destroyVariant($variantSKU)
 * @method static void       storeProductImages(Product $product, array $descriptions, array $files)
 * @method static void       setDefaultProductImage(Product $product, int $imageId)
 * @method static Collection showCategories(Product $product)
 * @method static void       makeSpecificationNonVariable(Variant $variant, $valueCode)
 * @method static Collection showProductImages(Product $product)
 * @method static Collection showProductSpecification(Product $product)
 * @method static void       storeProductSpecification(Product $product, array $valueCodes)
 * @method static void       destroyProductSpecification(Product $product, array $valueCodes)
 * @method static void       destroyProductImage(Product $product, int $imageId)
 * @method static void       storeVariantImages(Variant $variant, array $descriptions, array $files)
 * @method static void       makeSpecificationVariable(Product $product, $valueCode)
 * @method static void       updateCategories(Product $product, array $categoryCodes)
 */
class Products extends Facade
{
    const INTERFACE_BIND_NAME = ProductsInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
