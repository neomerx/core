<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Models\Category as CategoryModel;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Products\Related as RelatedApi;
use \Neomerx\Core\Models\ProductTaxType as TaxTypeModel;
use \Neomerx\Core\Models\Manufacturer as ManufacturerModel;
use \Neomerx\Core\Models\ProductImage as ProductImageModel;
use \Neomerx\Core\Api\Products\Categories as CategoriesApi;
use \Neomerx\Core\Models\Specification as SpecificationModel;
use \Neomerx\Core\Api\Products\ProductCrud as ProductCrudApi;
use \Neomerx\Core\Api\Products\VariantCrud as VariantCrudApi;
use \Neomerx\Core\Models\ProductProperties as PropertiesModel;
use \Neomerx\Core\Models\ProductRelated as ProductRelatedModel;
use \Neomerx\Core\Api\Products\ProductImage as ProductImageApi;
use \Neomerx\Core\Api\Products\VariantImage as VariantImageApi;
use \Neomerx\Core\Models\ProductCategory as ProductCategoryModel;
use \Neomerx\Core\Models\VariantProperties as VariantPropertiesModel;
use \Neomerx\Core\Models\CharacteristicValue as CharacteristicValueModel;
use \Neomerx\Core\Api\Products\ProductSpecification as ProductSpecificationApi;
use \Neomerx\Core\Api\Products\VariantSpecification as VariantSpecificationApi;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Products implements ProductsInterface
{
    const EVENT_PREFIX = 'Api.Product.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var CategoriesApi
     */
    private $categories;

    /**
     * @var RelatedApi
     */
    private $related;

    /**
     * @var ProductSpecificationApi
     */
    private $productSpecification;

    /**
     * @var VariantSpecificationApi
     */
    private $variantSpecification;

    /**
     * @var ProductImageApi
     */
    private $productImage;

    /**
     * @var VariantImageApi
     */
    private $variantImage;

    /**
     * @var ProductCrudApi
     */
    private $productCrud;

    /**
     * @var VariantCrudApi
     */
    private $variantCrud;

    /**
     * @param Model                    $model
     * @param PropertiesModel          $properties
     * @param LanguageModel            $language
     * @param ManufacturerModel        $manufacturer
     * @param CategoryModel            $category
     * @param ProductCategoryModel     $productCategory
     * @param ProductRelatedModel      $productRelated
     * @param CharacteristicValueModel $characteristicValue
     * @param VariantModel             $variant
     * @param SpecificationModel       $specification
     * @param ProductImageModel        $productImage
     * @param VariantPropertiesModel   $variantProperties
     * @param TaxTypeModel             $taxType
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Model $model,
        PropertiesModel $properties,
        LanguageModel $language,
        ManufacturerModel $manufacturer,
        CategoryModel $category,
        ProductCategoryModel $productCategory,
        ProductRelatedModel $productRelated,
        CharacteristicValueModel $characteristicValue,
        VariantModel $variant,
        SpecificationModel $specification,
        ProductImageModel $productImage,
        VariantPropertiesModel $variantProperties,
        TaxTypeModel $taxType
    ) {
        $this->productCrud = new ProductCrudApi($model, $properties, $category, $manufacturer, $taxType, $language);
        $this->variantCrud = new VariantCrudApi($model, $variant, $variantProperties, $language);

        $this->categories = new CategoriesApi($model, $category, $productCategory);
        $this->related    = new RelatedApi($model, $productRelated, ProductCrudApi::$relations);

        $this->productImage = new ProductImageApi($model, $productImage, $language);
        $this->variantImage = new VariantImageApi($variant, $productImage, $language);

        $this->productSpecification = new ProductSpecificationApi($model, $specification, $characteristicValue);
        $this->variantSpecification = new VariantSpecificationApi($variant, $characteristicValue);
    }

    /**
     * {@inheritdoc}
     */
    public function showCategories(Model $product)
    {
        return $this->categories->showCategories($product);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCategories(Model $product, array $categoryCodes)
    {
        $this->categories->updateCategories($product, $categoryCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function showRelated(Model $product)
    {
        return $this->related->showRelated($product);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRelated(Model $product, array $productSKUs)
    {
        $this->related->updateRelated($product, $productSKUs);
    }

    /**
     * {@inheritdoc}
     */
    public function showProductSpecification(Model $product)
    {
        return $this->productSpecification->showProductSpecification($product);
    }

    /**
     * {@inheritdoc}
     */
    public function storeProductSpecification(Model $product, array $valueCodes)
    {
        $this->productSpecification->storeProductSpecification($product, $valueCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyProductSpecification(Model $product, array $valueCodes)
    {
        $this->productSpecification->destroyProductSpecification($product, $valueCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function updateProductSpecification(Model $product, array $parameters = [])
    {
        $this->productSpecification->updateProductSpecification($product, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function makeSpecificationVariable(Model $product, $valueCode)
    {
        $this->productSpecification->makeSpecificationVariable($product, $valueCode);
    }

    /**
     * {@inheritdoc}
     */
    public function updateVariantSpecification(VariantModel $variant, array $parameters = [])
    {
        $this->variantSpecification->updateVariantSpecification($variant, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function makeSpecificationNonVariable(VariantModel $variant, $valueCode)
    {
        $this->variantSpecification->makeSpecificationNonVariable($variant, $valueCode);
    }

    /**
     * {@inheritdoc}
     */
    public function showProductImages(Model $product)
    {
        return $this->productImage->showProductImages($product);
    }

    /**
     * {@inheritdoc}
     */
    public function storeProductImages(Model $product, array $descriptions, array $files)
    {
        $this->productImage->storeProductImages($product, $descriptions, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProductImage(Model $product, $imageId)
    {
        $this->productImage->setDefaultProductImage($product, $imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyProductImage(Model $product, $imageId)
    {
        $this->productImage->destroyProductImage($product, $imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function showVariantImages(VariantModel $variant)
    {
        return $this->variantImage->showVariantImages($variant);
    }

    /**
     * {@inheritdoc}
     */
    public function storeVariantImages(VariantModel $variant, array $descriptions, array $files)
    {
        $this->variantImage->storeVariantImages($variant, $descriptions, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function destroyVariantImage(VariantModel $variant, $imageId)
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
    public function storeVariant(Model $product, array $input)
    {
        $this->variantCrud->create($product, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function updateVariant(VariantModel $variant, array $input)
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
