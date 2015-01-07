<?php namespace Neomerx\Core\Api\Categories;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Category as Model;
use \Neomerx\Core\Models\Product as ProductModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CategoryProperties as PropertiesModel;
use \Neomerx\Core\Models\ProductCategory as ProductCategoryModel;

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
     * @var Model
     */
    private $categoryModel;

    /**
     * @var PropertiesModel
     */
    private $propertiesModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @var ProductModel
     */
    private $productModel;

    /**
     * @var ProductCategoryModel
     */
    private $productCategoryModel;

    /**
     * @param Model                $category
     * @param PropertiesModel      $properties
     * @param LanguageModel        $language
     * @param ProductModel         $product
     * @param ProductCategoryModel $productCategory
     */
    public function __construct(
        Model $category,
        PropertiesModel $properties,
        LanguageModel $language,
        ProductModel $product,
        ProductCategoryModel $productCategory
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

            /** @var Model $category */
            $category = $this->categoryModel->createOrFailResource($input);
            Permissions::check($category, Permission::create());

            $categoryId = $category->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge(
                    [Model::FIELD_ID => $categoryId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $category */
        $category = $this->categoryModel->selectByCode($code)->withProperties()->firstOrFail();
        Permissions::check($category, Permission::view());
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function readDescendants(Model $parent)
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

        /** @var Model $category */
        $category = $this->categoryModel->selectByCode($code)->firstOrFail();
        Permissions::check($category, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            empty($input) ?: $category->updateOrFail($input);

            $categoryId = $category->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var PropertiesModel $property */
                $property = $this->propertiesModel->updateOrCreate(
                    [Model::FIELD_ID => $categoryId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $category */
        $category = $this->categoryModel->selectByCode($code)->firstOrFail();
        Permissions::check($category, Permission::delete());
        $category->deleteOrFail();

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'deleted', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function moveUp(Model $category)
    {
        Permissions::check($category, Permission::edit());
        $category->moveLeft();
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'movedUp', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function moveDown(Model $category)
    {
        Permissions::check($category, Permission::edit());
        $category->moveRight();
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'movedDown', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function attach(Model $category, Model $newParent)
    {
        Permissions::check($category, Permission::edit());
        Permissions::check($newParent, Permission::edit());
        $category->attachToCategory($newParent);
        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'attached', $category));
    }

    /**
     * {@inheritdoc}
     */
    public function showProducts(Model $category)
    {
        Permissions::check($category, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        $productsInCategory = $category->assignedProducts()->with('properties.language')->get();

        $result = [];
        foreach ($productsInCategory as $categoryProduct) {
            $position = $categoryProduct->pivot->{ProductCategoryModel::FIELD_POSITION};
            $result[] = [$categoryProduct, $position];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePositions(Model $category, array $productPositions)
    {
        $categoryId = $category->{Model::FIELD_ID};
        Permissions::check($category, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($productPositions as $sku => $position) {

                /** @var ProductModel $product */
                $product = $this->productModel->selectByCode($sku)->firstOrFail([ProductModel::FIELD_ID]);
                $productId = $product->{ProductModel::FIELD_ID};
                /** @noinspection PhpUndefinedMethodInspection */
                $this->productCategoryModel
                    ->where(ProductCategoryModel::FIELD_ID_CATEGORY, '=', $categoryId)
                    ->where(ProductCategoryModel::FIELD_ID_PRODUCT, '=', $productId)
                    ->update([ProductCategoryModel::FIELD_POSITION => $position]);

            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CategoryArgs(self::EVENT_PREFIX . 'updatedPositions', $category));
    }
}
