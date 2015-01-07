<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\ProductImage;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\ProductProperties;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Products\ProductsInterface as Api;

/**
 * This converter includes the following fields into conversion result:
 * - product main properties (SKU, price, package dimensions, etc)
 * - language properties (name, descriptions, meta fields)
 * - cover image information (path, formats, dimensions)
 */
class ProductConverterSmallDescription implements ConverterInterface
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var string
     */
    private $languageFilter;

    /**
     * @var ConverterInterface
     */
    private $imageConverter;

    /**
     * @param string             $languageFilter
     * @param ConverterInterface $imageConverter
     */
    public function __construct($languageFilter = null, ConverterInterface $imageConverter = null)
    {
        $this->languageFilter = $languageFilter;
        /** @noinspection PhpUndefinedMethodInspection */
        $this->imageConverter = $imageConverter ? $imageConverter : App::make(ImageConverterGeneric::BIND_NAME);
    }

    /**
     * @param string $languageFilter
     */
    public function setLanguageFilter($languageFilter)
    {
        $this->languageFilter = $languageFilter;
    }

    /**
     * @return string
     */
    public function getLanguageFilter()
    {
        return $this->languageFilter;
    }

    /**
     * Format model to array representation.
     *
     * @param Product $product
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function convert($product = null)
    {
        if ($product === null) {
            return null;
        }

        ($product instanceof Product) ?: S\throwEx(new InvalidArgumentException('product'));

        $result = $product->attributesToArray();

        // find cover image (either first or which is specified as cover)
        /** @var ProductImage $coverImage */
        $coverImage = null;
        foreach ($product->product_images as $image) {
            /** @var ProductImage $image */
            if ($coverImage === null) {
                $coverImage = $image;
            }
            if ($image->is_cover) {
                $coverImage = $image;
                break;
            }
        }

        $result[Api::PARAM_IMAGES]     = $coverImage ? [$this->imageConverter->convert($coverImage->image)] : [];
        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $product->properties,
            ProductProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
