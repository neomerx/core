<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Cache\TagTrait;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Cache\TagProviderInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class VariantTagProvider implements TagProviderInterface
{
    use TagTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @param Variant $variant
     *
     * @return array
     */
    public function getTags($variant)
    {
        $variant instanceof Variant ?: S\throwEx(new InvalidArgumentException('variant'));
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
