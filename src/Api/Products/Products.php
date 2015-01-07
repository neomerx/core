<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Models\Specification;
use \Neomerx\Core\Models\ProductRelated;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Models\VariantProperties;
use \Neomerx\Core\Models\CharacteristicValue;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Products implements ProductsInterface
{
    const EVENT_PREFIX = 'Api.Product.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Categories
     */
    private $categories;

    /**
     * @var Related
     */
    private $related;

    /**
     * @var ProductSpecification
     */
    private $productSpecification;

    /**
     * @var VariantSpecification
     */
    private $variantSpecification;

    /**
     * @var ProductImages
     */
    private $productImage;

    /**
     * @var VariantImage
     */
    private $variantImage;

    /**
     * @var ProductCrud
     */
    private $productCrud;

    /**
     * @var VariantCrud
     */
    private $variantCrud;

    /**
     * @param Product             $product
     * @param ProductProperties   $properties
     * @param Language            $language
     * @param Manufacturer        $manufacturer
     * @param Category            $category
     * @param ProductCategory     $productCategory
     * @param ProductRelated      $productRelated
     * @param CharacteristicValue $characteristicValue
     * @param Variant             $variant
     * @param Specification       $specification
     * @param ProductImage        $productImage
     * @param VariantProperties   $variantProperties
     * @param ProductTaxType      $taxType
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Product $product,
        ProductProperties $properties,
        Language $language,
        Manufacturer $manufacturer,
        Category $category,
        ProductCategory $productCategory,
        ProductRelated $productRelated,
        CharacteristicValue $characteristicValue,
        Variant $variant,
        Specification $specification,
        ProductImage $productImage,
        VariantProperties $variantProperties,
        ProductTaxType $taxType
    ) {
        $this->productCrud = new ProductCrud($product, $properties, $category, $manufacturer, $taxType, $language);
        $this->variantCrud = new VariantCrud($product, $variant, $variantProperties, $language);

        $this->categories = new Categories($product, $category, $productCategory);
        $this->related    = new Related($product, $productRelated, ProductCrud::$relations);

        $this->productImage = new ProductImages($product, $productImage, $language);
        $this->variantImage = new VariantImage($variant, $productImage, $language);

        $this->productSpecification = new ProductSpecification($product, $specification, $characteristicValue);
        $this->variantSpecification = new VariantSpecification($variant, $characteristicValue);
    }

    /**
     * {@inheritdoc}
     */
    public function showCategories(Product $product)
    {
        return $this->categories->showCategories($product);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCategories(Product $product, array $categoryCodes)
    {
        $this->categories->updateCategories($product, $categoryCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function showRelated(Product $product)
    {
        return $this->related->showRelated($product);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRelated(Product $product, array $productSKUs)
    {
        $this->related->updateRelated($product, $productSKUs);
    }

    /**
     * {@inheritdoc}
     */
    public function showProductSpecification(Product $product)
    {
        return $this->productSpecification->showProductSpecification($product);
    }

    /**
     * {@inheritdoc}
     */
    public function storeProductSpecification(Product $product, array $valueCodes)
    {
        $this->productSpecification->storeProductSpecification($product, $valueCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyProductSpecification(Product $product, array $valueCodes)
    {
        $this->productSpecification->destroyProductSpecification($product, $valueCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function updateProductSpecification(Product $product, array $parameters = [])
    {
        $this->productSpecification->updateProductSpecification($product, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function makeSpecificationVariable(Product $product, $valueCode)
    {
        $this->productSpecification->makeSpecificationVariable($product, $valueCode);
    }

    /**
     * {@inheritdoc}
     */
    public function updateVariantSpecification(Variant $variant, array $parameters = [])
    {
        $this->variantSpecification->updateVariantSpecification($variant, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function makeSpecificationNonVariable(Variant $variant, $valueCode)
    {
        $this->variantSpecification->makeSpecificationNonVariable($variant, $valueCode);
    }

    /**
     * {@inheritdoc}
     */
    public function showProductImages(Product $product)
    {
        return $this->productImage->showProductImages($product);
    }

    /**
     * {@inheritdoc}
     */
    public function storeProductImages(Product $product, array $descriptions, array $files)
    {
        $this->productImage->storeProductImages($product, $descriptions, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProductImage(Product $product, $imageId)
    {
        $this->productImage->setDefaultProductImage($product, $imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyProductImage(Product $product, $imageId)
    {
        $this->productImage->destroyProductImage($product, $imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function showVariantImages(Variant $variant)
    {
        return $this->variantImage->showVariantImages($variant);
    }

    /**
     * {@inheritdoc}
     */
    public function storeVariantImages(Variant $variant, array $descriptions, array $files)
    {
        $this->variantImage->storeVariantImages($variant, $descriptions, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyVariantImage(Variant $variant, $imageId)
    {
        $this->variantImage->destroyVariantImage($variant, $imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        return $this->productCrud->create($input);
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        return $this->productCrud->read($code);
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        return $this->productCrud->search($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function update($sku, array $input)
    {
        $this->productCrud->update($sku, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($sku)
    {
        $this->productCrud->delete($sku);
    }

    /**
     * {@inheritdoc}
     */
    public function storeVariant(Product $product, array $input)
    {
        $this->variantCrud->create($product, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function updateVariant(Variant $variant, array $input)
    {
        $this->variantCrud->update($variant, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyVariant($variantSKU)
    {
        $this->variantCrud->delete($variantSKU);
    }
}
