<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Models\Category;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Categories;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Converters\ProductConverterGeneric;
use \Neomerx\Core\Converters\CategoryConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class CategoriesControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Categories::INTERFACE_BIND_NAME, App::make(CategoryConverterGeneric::BIND_NAME));
    }

    /**
     * Read descendants for category.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function descendants($code)
    {
        settype($code, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('readDescendants', [$code, $this->getLanguageFilter(Input::all())]);
    }

    /**
     * Read top level categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function top()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('readTopCategories', [$parameters]);
    }

    /**
     * Move the category up among children of its parent.
     * The category will not change its ancestor.
     *
     * @param $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function moveUp($code)
    {
        settype($code, 'string');
        return $this->tryAndCatchWrapper('moveUpImpl', [$code]);
    }

    /**
     * Move the category down among children of its parent.
     * The category will not change its ancestor.
     *
     * @param $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function moveDown($code)
    {
        settype($code, 'string');
        return $this->tryAndCatchWrapper('moveDownImpl', [$code]);
    }

    /**
     * Attach category with code $code as a descendant to category with code $codeTo.
     *
     * @param string $code
     * @param string $codeTo
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function attach($code, $codeTo)
    {
        settype($code, 'string');
        return $this->tryAndCatchWrapper('attachImpl', [$code, $codeTo]);
    }

    /**
     * Show products in categories.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function showProducts($code)
    {
        settype($code, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('showProductsImpl', [$code, $this->getLanguageFilter(Input::all())]);
    }

    /**
     * Update product positions in category.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updatePositions($code)
    {
        settype($code, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $productPositions = Input::all();
        return $this->tryAndCatchWrapper('updatePositionsImpl', [$code, $productPositions]);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function readTopCategories(array $parameters)
    {
        $categories = $this->getApiFacade()->readDescendants(
            $this->getModelByCode(Category::BIND_NAME, Category::ROOT_CODE),
            $parameters
        );
        return [$categories, null];
    }

    /**
     * @param string $code
     * @param string $languageFilter
     *
     * @return array
     */
    protected function readDescendants($code, $languageFilter)
    {
        $categories = $this->getApiFacade()->readDescendants(
            $this->getModelByCode(Category::BIND_NAME, $code)
        );

        /** @var CategoryConverterGeneric $converter */
        $converter = $this->getConverter();
        if (!empty($languageFilter)) {
            $converter->setLanguageFilter($languageFilter);
        }

        $result = [];
        foreach ($categories as $category) {
            $result[] = $converter->convert($category);
        }


        return [$result, null];
    }

    /**
     * @param string $code
     *
     * @return array
     */
    protected function moveUpImpl($code)
    {
        $this->getApiFacade()->moveUp($this->getModelByCode(Category::BIND_NAME, $code));
        return [null, null];
    }

    /**
     * @param string $code
     *
     * @return array
     */
    protected function moveDownImpl($code)
    {
        $this->getApiFacade()->moveDown($this->getModelByCode(Category::BIND_NAME, $code));
        return [null, null];
    }

    /**
     * @param string $code
     * @param string $codeTo
     *
     * @return array
     */
    protected function attachImpl($code, $codeTo)
    {
        $this->getApiFacade()->attach(
            $this->getModelByCode(Category::BIND_NAME, $code),
            $this->getModelByCode(Category::BIND_NAME, $codeTo)
        );
        return [null, null];
    }

    /**
     * @param string $code
     * @param string $languageFilter
     *
     * @return array
     */
    protected function showProductsImpl($code, $languageFilter)
    {
        $productAndPosPairs = $this->getApiFacade()->showProducts($this->getModelByCode(Category::BIND_NAME, $code));

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var ProductConverterGeneric $productConverter */
        $productConverter = App::make(ProductConverterGeneric::BIND_NAME);
        $productConverter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($productAndPosPairs as $pair) {
            list($product, $position) = $pair;
            $result[] = array_merge(
                $productConverter->convert($product),
                [ProductCategory::FIELD_POSITION => $position]
            );
        }

        return [$result, null];
    }

    /**
     * @param string $code
     * @param array  $productPositions
     *
     * @return array
     */
    protected function updatePositionsImpl($code, array $productPositions)
    {
        $products = $this->getApiFacade()->updatePositions(
            $this->getModelByCode(Category::BIND_NAME, $code),
            $productPositions
        );
        return [$products, null];
    }
}
