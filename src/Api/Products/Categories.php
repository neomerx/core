<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Exceptions\ValidationException;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\Category as CategoryModel;
use \Neomerx\Core\Models\ProductCategory as ProductCategoryModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Categories
{

    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var CategoryModel
     */
    private $categoryModel;

    /**
     * @var ProductCategoryModel
     */
    private $productCategoryModel;

    /**
     * @param Model                $product
     * @param CategoryModel        $category
     * @param ProductCategoryModel $productCategory
     */
    public function __construct(Model $product, CategoryModel $category, ProductCategoryModel $productCategory)
    {
        $this->productModel         = $product;
        $this->categoryModel        = $category;
        $this->productCategoryModel = $productCategory;
    }

    /**
     * Show product categories.
     *
     * @param Model $product
     *
     * @return Collection
     */
    public function showCategories(Model $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $categories = $product->assignedCategories()->withProperties()->get();
        return $categories;
    }

    /**
     * Set categories to product.
     *
     * @param Model $product
     * @param array $categoryCodes
     *
     * @return void
     */
    public function updateCategories(Model $product, array $categoryCodes)
    {
        Permissions::check($product, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        $currentCategoryIds = $product->productCategories()->lists(CategoryModel::FIELD_ID);
        $categoryIds        = $this->categoryModel->selectByCodes($categoryCodes)->lists(CategoryModel::FIELD_ID);

        $toAddIds    = array_diff($categoryIds, $currentCategoryIds);
        $toRemoveIds = array_diff($currentCategoryIds, $categoryIds);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // Add
            foreach ($toAddIds as $newCategoryId) {
                /** @noinspection PhpUndefinedMethodInspection */
                $lastPosInTheCategory = $this->productCategoryModel->selectMaxPosition($newCategoryId);
                $lastPosInTheCategory = ($lastPosInTheCategory === null ? 1 : ++$lastPosInTheCategory);
                /** @var ProductCategoryModel $productCategory */
                /** @noinspection PhpUndefinedMethodInspection */
                $productCategory = App::make(ProductCategoryModel::BIND_NAME);
                $productCategory->fill([
                    ProductCategoryModel::FIELD_ID_CATEGORY => $newCategoryId,
                    ProductCategoryModel::FIELD_POSITION    => $lastPosInTheCategory,
                ]);

                /** @noinspection PhpUndefinedMethodInspection */
                $isCategoryAdded =  $product->productCategories()->save($productCategory);
                $isCategoryAdded =  ($isCategoryAdded and $isCategoryAdded->exists);
                $isCategoryAdded ?: S\throwEx(new ValidationException($productCategory->getValidator()));

            }

            // Remove
            /** @noinspection PhpUndefinedMethodInspection */
            $toRemove = $product->productCategories()
                ->whereIn(CategoryModel::FIELD_ID, $toRemoveIds)
                ->lists(ProductCategoryModel::FIELD_ID);
            $this->productCategoryModel->destroy($toRemove);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updatedCategories', $product));
    }
}
