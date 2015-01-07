<?php namespace Neomerx\Core\Api\Categories;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CategoryProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Categories implements CategoriesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Category.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Category
     */
    private $categoryModel;

    /**
     * @var CategoryProperties
     */
    private $propertiesModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var ProductCategory
     */
    private $productCategoryModel;

    /**
     * @param Category           $category
     * @param CategoryProperties $properties
     * @param Language           $language
     * @param Product            $product
     * @param ProductCategory    $productCategory
     */
    public function __construct(
        Category $category,
        CategoryProperties $properties,
        Language $language,
        Product $product,
        ProductCategory $productCategory
    ) {
        $this->categoryModel        = $category;
        $this->propertiesModel      = $properties;
        $this->languageModel        = $language;
        $this->productModel         = $product;
        $this->productCategoryModel = $productCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        // check language properties are not empty
        count($propertiesInput) ? null : S\throwEx(new InvalidArgumentException('properties'));

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Category $category */
            $category = $this->categoryModel->createOrFailResource($input);
            Permissions::check($category, Permission::create());

            $categoryId = $category->{Category::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge(
                    [Category::FIELD_ID => $categoryId, Language::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'created', $category));

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Category $category */
        $category = $this->categoryModel->selectByCode($code)->withProperties()->firstOrFail();
        Permissions::check($category, Permission::view());
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function readDescendants(Category $parent)
    {
        Permissions::check($parent, Permission::view());
        return $parent->getDescendants();
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        /** @var Category $category */
        $category = $this->categoryModel->selectByCode($code)->firstOrFail();
        Permissions::check($category, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            empty($input) ?: $category->updateOrFail($input);

            $categoryId = $category->{Category::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var CategoryProperties $property */
                $property = $this->propertiesModel->updateOrCreate(
                    [Category::FIELD_ID => $categoryId, Language::FIELD_ID => $languageId],
                    $propertyInput
                );
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'updated', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Category $category */
        $category = $this->categoryModel->selectByCode($code)->firstOrFail();
        Permissions::check($category, Permission::delete());
        $category->deleteOrFail();

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'deleted', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function moveUp(Category $category)
    {
        Permissions::check($category, Permission::edit());
        $category->moveLeft();
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'movedUp', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function moveDown(Category $category)
    {
        Permissions::check($category, Permission::edit());
        $category->moveRight();
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'movedDown', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function attach(Category $category, Category $newParent)
    {
        Permissions::check($category, Permission::edit());
        Permissions::check($newParent, Permission::edit());
        $category->attachToCategory($newParent);
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'attached', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function showProducts(Category $category)
    {
        Permissions::check($category, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        $productsInCategory = $category->assignedProducts()->with('properties.language')->get();

        $result = [];
        foreach ($productsInCategory as $categoryProduct) {
            $position = $categoryProduct->pivot->{ProductCategory::FIELD_POSITION};
            $result[] = [$categoryProduct, $position];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePositions(Category $category, array $productPositions)
    {
        $categoryId = $category->{Category::FIELD_ID};
        Permissions::check($category, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($productPositions as $sku => $position) {

                /** @var Product $product */
                $product = $this->productModel->selectByCode($sku)->firstOrFail([Product::FIELD_ID]);
                $productId = $product->{Product::FIELD_ID};
                /** @noinspection PhpUndefinedMethodInspection */
                $this->productCategoryModel
                    ->where(ProductCategory::FIELD_ID_CATEGORY, '=', $categoryId)
                    ->where(ProductCategory::FIELD_ID_PRODUCT, '=', $productId)
                    ->update([ProductCategory::FIELD_POSITION => $position]);

            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'updatedPositions', $category));
    }
}
