<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Cache\TagTrait;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Category;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Cache\ItemProviderInterface;

class VariantCacheProvider implements ItemProviderInterface
{
    use TagTrait;

    const BIND_NAME    = __CLASS__;
    const CACHE_PREFIX = 'nm_variant__';

    /**
     * @var array Variant relations to be loaded.
     */
    private $relations;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     *
     */
    public function __construct()
    {
        $this->relations = [
            'product.defaultCategory',
            'product.manufacturer',
            'product.taxType',
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $this->variantModel = App::make(Variant::BIND_NAME);
    }

    /**
     * Get tag for object.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getTag($objectId)
    {
        return $this->composeTag(Variant::BIND_NAME, $objectId);
    }

    /**
     * Get product or variant by SKU.
     *
     * @param string $sku
     *
     * @return array    [object, [related key 1, related key 1, ...]] If object has data from other
     * objects then keys to related objects should be specified (for proper cache cleaning on updates).
     */
    public function getObject($sku)
    {
        /** @var \Neomerx\Core\Models\Variant $variant */
        $variant = $this->variantModel->selectByCode($sku)->with($this->relations)->firstOrFail();
        return [$variant, $this->getTags($variant)];
    }

    /**
     * Get multiple objects by IDs at a time.
     *
     * @param array $skuArray
     *
     * @return array            [sku => object (see getObject output format), ...]
     */
    public function getObjects(array $skuArray)
    {
        $variants = $this->variantModel->selectByCodes($skuArray)->with($this->relations)->get();

        $result = [];
        /** @var \Neomerx\Core\Models\Variant $variant */
        foreach ($variants as $variant) {
            $result[$variant->sku] = [$variant, $this->getTags($variant)];
        }

        return $result;
    }

    /**
     * Get a key to be used to store a object in cache.
     * The key could be used by external systems to work with an underlying cache engine.
     *
     * @param string $sku
     *
     * @return string
     */
    public function getKey($sku)
    {
        return self::CACHE_PREFIX . $sku;
    }

    /**
     * @param Variant $variant
     *
     * @return array
     */
    private function getTags(Variant $variant)
    {
        $product = $variant->product;
        return [
            $this->composeTag(Variant::BIND_NAME, $variant->sku),
            $this->composeTag(Product::BIND_NAME, $product->sku),
            $this->composeTag(ProductTaxType::BIND_NAME, $product->tax_type->code),
            $this->composeTag(Category::BIND_NAME, $product->default_category->code),
            $this->composeTag(Manufacturer::BIND_NAME, $product->manufacturer->code),
        ];
    }
}
