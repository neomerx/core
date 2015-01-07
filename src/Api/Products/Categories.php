<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Category;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\ProductCategory;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Categories
{

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var Category
     */
    private $categoryModel;

    /**
     * @var ProductCategory
     */
    private $productCategoryModel;

    /**
     * @param Product         $product
     * @param Category        $category
     * @param ProductCategory $productCategory
     */
    public function __construct(Product $product, Category $category, ProductCategory $productCategory)
    {
        $this->productModel         = $product;
        $this->categoryModel        = $category;
        $this->productCategoryModel = $productCategory;
    }

    /**
     * Show product categories.
     *
     * @param Product $product
     *
     * @return Collection
     */
    public function showCategories(Product $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $categories = $product->assignedCategories()->withProperties()->get();
        return $categories;
    }

    /**
     * Set categories to product.
     *
     * @param Product $product
     * @param array $categoryCodes
     *
     * @return void
     */
    public function updateCategories(Product $product, array $categoryCodes)
    {
        Permissions::check($product, Permission::edit());

        /** @noinspection PhpUndefinedMethodInspection */
        $currentCategoryIds = $product->productCategories()->lists(Category::FIELD_ID);
        $categoryIds        = $this->categoryModel->selectByCodes($categoryCodes)->lists(Category::FIELD_ID);

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
                /** @var ProductCategory $productCategory */
                /** @noinspection PhpUndefinedMethodInspection */
                $productCategory = App::make(ProductCategory::BIND_NAME);
                $productCategory->fill([
                    ProductCategory::FIELD_ID_CATEGORY => $newCategoryId,
                    ProductCategory::FIELD_POSITION    => $lastPosInTheCategory,
                ]);

                /** @noinspection PhpUndefinedMethodInspection */
                $isCategoryAdded =  $product->productCategories()->save($productCategory);
                $isCategoryAdded =  ($isCategoryAdded and $isCategoryAdded->exists);
                $isCategoryAdded ?: S\throwEx(new ValidationException($productCategory->getValidator()));

            }

            // Remove
            /** @noinspection PhpUndefinedMethodInspection */
            $toRemove = $product->productCategories()
                ->whereIn(Category::FIELD_ID, $toRemoveIds)
                ->lists(ProductCategory::FIELD_ID);
            $this->productCategoryModel->destroy($toRemove);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updatedCategories', $product));
    }
}
