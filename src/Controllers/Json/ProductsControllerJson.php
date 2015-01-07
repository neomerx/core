<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Models\ProductImage;
use \Neomerx\Core\Api\Facades\Products;
use \Neomerx\Core\Converters\ConverterInterface;
use \Neomerx\Core\Converters\ProductConverterGeneric;
use \Neomerx\Core\Converters\CategoryConverterGeneric;
use \Neomerx\Core\Converters\ProductImageConverterGeneric;
use \Neomerx\Core\Converters\SpecificationConverterGeneric;
use \Neomerx\Core\Converters\ProductConverterSmallDescription;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class ProductsControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Products::INTERFACE_BIND_NAME, App::make(ProductConverterGeneric::BIND_NAME));
    }

    /**
     * Search products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$parameters]);
    }

    /**
     * Read product additional categories.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showCategories($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('showCategoriesImpl', [$productSKU, $this->getLanguageFilter($input)]);
    }

    /**
     * Set categories to product.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateCategories($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('updateCategoriesImpl', [$productSKU, $parameters]);
    }

    /**
     * Read related products.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showRelated($productSKU)
    {
        settype($productSKU, 'string');
        return $this->tryAndCatchWrapper('showRelatedImpl', [$productSKU]);
    }

    /**
     * Set related products.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateRelated($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('updateRelatedImpl', [$productSKU, $parameters]);
    }

    /**
     * Read product specification.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showProductSpecification($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper(
            'showProductSpecificationImpl',
            [$productSKU, $this->getLanguageFilter(Input::all())]
        );
    }

    /**
     * Add product specification.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeProductSpecification($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('storeProductSpecificationImpl', [$productSKU, $parameters]);
    }

    /**
     * Update product specification.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateProductSpecification($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('updateProductSpecificationImpl', [$productSKU, $parameters]);
    }

    /**
     * Update product specification.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyProductSpecification($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('destroyProductSpecificationImpl', [$productSKU, $parameters]);
    }

    /**
     * Read product images.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showProductImages($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper(
            'showProductImagesImpl',
            [$productSKU, $this->getLanguageFilter($input), $this->getImageFormatFilter($input)]
        );
    }

    /**
     * Add product images.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeProductImages($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        unset($parameters['images']);
        /** @noinspection PhpUndefinedMethodInspection */
        $files      = Input::file('images');
        return $this->tryAndCatchWrapper('storeProductImagesImpl', [$productSKU, $parameters, $files]);
    }

    /**
     * Remove product images.
     *
     * @param string $productSKU
     * @param int    $imageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyProductImage($productSKU, $imageId)
    {
        settype($productSKU, 'string');
        settype($imageId, 'int');
        return $this->tryAndCatchWrapper('destroyProductImageImpl', [$productSKU, $imageId]);
    }

    /**
     * Set product image as cover.
     *
     * @param $productSKU
     * @param $imageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function setDefaultProductImage($productSKU, $imageId)
    {
        settype($imageId, 'int');
        settype($productSKU, 'string');
        return $this->tryAndCatchWrapper('setDefaultProductImageImpl', [$productSKU, $imageId]);
    }

    /**
     * Add product variants.
     *
     * @param string $productSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeVariant($productSKU)
    {
        settype($productSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('storeVariantImpl', [$productSKU, $parameters]);
    }

    /**
     * Remove product variants.
     *
     * @param string $variantSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyVariant($variantSKU)
    {
        settype($variantSKU, 'string');
        return $this->tryAndCatchWrapper('destroyVariantImpl', [$variantSKU]);
    }

    /**
     * Make specification variable.
     *
     * @param string $productSKU
     * @param string $valueCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function makeSpecificationVariable($productSKU, $valueCode)
    {
        settype($productSKU, 'string');
        settype($valueCode, 'string');
        return $this->tryAndCatchWrapper('makeSpecificationVariableImpl', [$productSKU, $valueCode]);
    }

    /**
     * Update variant specification.
     *
     * @param string $variantSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateVariantSpecification($variantSKU)
    {
        settype($variantSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('updateVariantSpecificationImpl', [$variantSKU, $parameters]);
    }

    /**
     * Make specification non variable.
     *
     * @param string $variantSKU
     * @param string $valueCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function makeSpecificationNonVariable($variantSKU, $valueCode)
    {
        settype($variantSKU, 'string');
        settype($valueCode, 'string');
        return $this->tryAndCatchWrapper('makeSpecificationNonVariableImpl', [$variantSKU, $valueCode]);
    }

    /**
     * Read variant images.
     *
     * @param string $variantSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showVariantImages($variantSKU)
    {
        settype($variantSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper(
            'showVariantImagesImpl',
            [$variantSKU, $this->getLanguageFilter($input), $this->getImageFormatFilter($input)]
        );
    }

    /**
     * Add variant images.
     *
     * @param string $variantSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeVariantImages($variantSKU)
    {
        settype($variantSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        unset($parameters['images']);
        /** @noinspection PhpUndefinedMethodInspection */
        $files      = Input::file('images');
        return $this->tryAndCatchWrapper('storeVariantImagesImpl', [$variantSKU, $parameters, $files]);
    }

    /**
     * Remove variant images.
     *
     * @param string $variantSKU
     * @param int    $imageId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyVariantImage($variantSKU, $imageId)
    {
        settype($variantSKU, 'string');
        settype($imageId, 'int');
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('destroyVariantImageImpl', [$variantSKU, $imageId]);
    }

    /**
     * Update product variant.
     *
     * @param string $variantSKU
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateVariant($variantSKU)
    {
        settype($variantSKU, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('updateVariantImpl', [$variantSKU, $parameters]);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function searchImpl(array $parameters)
    {
        $resources = $this->getApiFacade()->search($parameters);
        return [$resources, null];
    }

    /**
     * @param string $productSKU
     * @param string $languageFilter
     *
     * @return array
     */
    protected function showCategoriesImpl($productSKU, $languageFilter)
    {
        /** @var CategoryConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(CategoryConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        $resources = $this->getApiFacade()->showCategories($this->getModelByCode(Product::BIND_NAME, $productSKU));
        foreach ($resources as $resource) {
            $result[] = $converter->convert($resource);
        }

        return [$result, null];
    }

    /**
     * @param string $productSKU
     * @param array $categoryCodes
     *
     * @return array
     */
    protected function updateCategoriesImpl($productSKU, array $categoryCodes)
    {
        $this->getApiFacade()->updateCategories($this->getModelByCode(Product::BIND_NAME, $productSKU), $categoryCodes);
        return [null, null];
    }

    /**
     * @param string $productSKU
     *
     * @return array
     */
    protected function showRelatedImpl($productSKU)
    {
        $result = [];

        /** @var ConverterInterface $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(ProductConverterSmallDescription::BIND_NAME);

        $related = $this->getApiFacade()->showRelated($this->getModelByCode(Product::BIND_NAME, $productSKU));
        foreach ($related as $product) {
            $result[] = $converter->convert($product);
        }

        return [$result, null];
    }

    /**
     * @param string $productSKU
     * @param array  $productSKUs
     *
     * @return array
     */
    protected function updateRelatedImpl($productSKU, array $productSKUs)
    {
        $this->getApiFacade()->updateRelated($this->getModelByCode(Product::BIND_NAME, $productSKU), $productSKUs);
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param string $languageFilter
     *
     * @return array
     */
    protected function showProductSpecificationImpl($productSKU, $languageFilter)
    {
        $specification = $this->getApiFacade()->showProductSpecification(
            $this->getModelByCode(Product::BIND_NAME, $productSKU)
        );
        /** @var SpecificationConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(SpecificationConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);
        $result = $converter->convert($specification);

        return [$result, null];
    }

    /**
     * @param string $productSKU
     * @param array  $parameters
     *
     * @return array
     */
    protected function storeProductSpecificationImpl($productSKU, array $parameters)
    {
        $this->getApiFacade()->storeProductSpecification(
            $this->getModelByCode(Product::BIND_NAME, $productSKU),
            $parameters
        );
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param array  $parameters
     *
     * @return array
     */
    protected function updateProductSpecificationImpl($productSKU, array $parameters)
    {
        $this->getApiFacade()->updateProductSpecification(
            $this->getModelByCode(Product::BIND_NAME, $productSKU),
            $parameters
        );
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param array  $valueCodes
     *
     * @return array
     */
    protected function destroyProductSpecificationImpl($productSKU, array $valueCodes)
    {
        $this->getApiFacade()->destroyProductSpecification(
            $this->getModelByCode(Product::BIND_NAME, $productSKU),
            $valueCodes
        );
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param string $languageFilter
     * @param string $formatFiler
     *
     * @return array
     */
    protected function showProductImagesImpl($productSKU, $languageFilter, $formatFiler)
    {
        $images = $this->getApiFacade()->showProductImages($this->getModelByCode(Product::BIND_NAME, $productSKU));

        /** @var ProductImageConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(ProductImageConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);
        $converter->setFormatFiler($formatFiler);

        $result = [];
        foreach ($images as $image) {
            /** @var ProductImage $image */
            $result[] = $converter->convert($image);
        }

        return [$result, null];
    }

    /**
     * @param string $productSKU
     * @param array  $parameters
     * @param array  $files
     *
     * @return array
     */
    protected function storeProductImagesImpl($productSKU, array $parameters, array $files)
    {
        $this->getApiFacade()->storeProductImages(
            $this->getModelByCode(Product::BIND_NAME, $productSKU),
            $parameters,
            $files
        );
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param int    $imageId
     *
     * @return array
     */
    protected function destroyProductImageImpl($productSKU, $imageId)
    {
        $this->getApiFacade()->destroyProductImage($this->getModelByCode(Product::BIND_NAME, $productSKU), $imageId);
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param int    $imageId
     *
     * @return array
     */
    protected function setDefaultProductImageImpl($productSKU, $imageId)
    {
        $this->getApiFacade()->setDefaultProductImage($this->getModelByCode(Product::BIND_NAME, $productSKU), $imageId);
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param array  $parameters
     *
     * @return array
     */
    protected function storeVariantImpl($productSKU, array $parameters)
    {
        $this->getApiFacade()->storeVariant($this->getModelByCode(Product::BIND_NAME, $productSKU), $parameters);
        return [null, null];
    }

    /**
     * @param string $variantSKU
     *
     * @return array
     */
    protected function destroyVariantImpl($variantSKU)
    {
        $this->getApiFacade()->destroyVariant($variantSKU);
        return [null, null];
    }

    /**
     * @param string $productSKU
     * @param string $valueCode
     *
     * @return array
     */
    protected function makeSpecificationVariableImpl($productSKU, $valueCode)
    {
        $this->getApiFacade()->makeSpecificationVariable(
            $this->getModelByCode(Product::BIND_NAME, $productSKU),
            $valueCode
        );
        return [null, null];
    }

    /**
     * @param string $variantSKU
     * @param array  $parameters
     *
     * @return array
     */
    protected function updateVariantSpecificationImpl($variantSKU, array $parameters)
    {
        $this->getApiFacade()->updateVariantSpecification(
            $this->getModelByCode(Variant::BIND_NAME, $variantSKU),
            $parameters
        );
        return [null, null];
    }

    /**
     * @param string $variantSKU
     * @param string $valueCode
     *
     * @return array
     */
    protected function makeSpecificationNonVariableImpl($variantSKU, $valueCode)
    {
        $this->getApiFacade()->makeSpecificationNonVariable(
            $this->getModelByCode(Variant::BIND_NAME, $variantSKU),
            $valueCode
        );
        return [null, null];
    }

    /**
     * @param string $variantSKU
     * @param string $languageFilter
     * @param string $formatFiler
     *
     * @return array
     */
    protected function showVariantImagesImpl($variantSKU, $languageFilter, $formatFiler)
    {
        $images = $this->getApiFacade()->showVariantImages($this->getModelByCode(Variant::BIND_NAME, $variantSKU));

        /** @var ProductImageConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(ProductImageConverterGeneric::BIND_NAME);
        $converter->setLanguageFilter($languageFilter);
        $converter->setFormatFiler($formatFiler);

        $result = [];
        foreach ($images as $image) {
            /** @var ProductImage $image */
            $result[] = $converter->convert($image);
        }

        return [$result, null];
    }

    /**
     * @param string $variantSKU
     * @param array  $parameters
     * @param array  $files
     *
     * @return array
     */
    protected function storeVariantImagesImpl($variantSKU, array $parameters, array $files)
    {
        $this->getApiFacade()->storeVariantImages(
            $this->getModelByCode(Variant::BIND_NAME, $variantSKU),
            $parameters,
            $files
        );
        return [null, null];
    }

    /**
     * @param string $variantSKU
     * @param int    $imageId
     *
     * @return array
     */
    protected function destroyVariantImageImpl($variantSKU, $imageId)
    {
        $this->getApiFacade()->destroyVariantImage($this->getModelByCode(Variant::BIND_NAME, $variantSKU), $imageId);
        return [null, null];
    }

    /**
     * @param string $variantSKU
     * @param array  $parameters
     *
     * @return array
     */
    protected function updateVariantImpl($variantSKU, array $parameters)
    {
        $this->getApiFacade()->updateVariant($this->getModelByCode(Variant::BIND_NAME, $variantSKU), $parameters);
        return [null, null];
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function getImageFormatFilter(array $input)
    {
        return S\array_get_value($input, 'format');
    }
}
