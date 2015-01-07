<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\ProductRelated;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Related
{
    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var ProductRelated
     */
    private $productRelatedModel;

    /**
     * @var array
     */
    private $relations;

    /**
     * @param Product        $product
     * @param ProductRelated $productRelated
     * @param array          $modelRelations
     */
    public function __construct(Product $product, ProductRelated $productRelated, array $modelRelations)
    {
        $this->productModel        = $product;
        $this->productRelatedModel = $productRelated;
        $this->relations           = $modelRelations;
    }

    /**
     * Read related products.
     *
     * @param Product $product
     *
     * @return Collection
     */
    public function showRelated(Product $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $related = $product->relatedProducts()->with($this->relations)->get();
        return $related;
    }

    /**
     * Set related products to product.
     *
     * @param Product $product
     * @param array $productSKUs
     *
     * @return void
     */
    public function updateRelated(Product $product, array $productSKUs)
    {
        Permissions::check($product, Permission::edit());

        $productIds  = $this->productModel->selectByCodes($productSKUs)->lists(Product::FIELD_ID);
        /** @noinspection PhpUndefinedMethodInspection */
        $relatedIds  = $product->related()->lists(ProductRelated::FIELD_ID_RELATED_PRODUCT);
        $toAdd       = array_diff($productIds, $relatedIds);
        $toRemove    = array_diff($relatedIds, $productIds);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // Add
            foreach ($toAdd as $relatedProductId) {
                /** @var ProductRelated $relatedProduct */
                /** @noinspection PhpUndefinedMethodInspection */
                $relatedProduct = App::make(ProductRelated::BIND_NAME);
                $relatedProduct->fill([
                    ProductRelated::FIELD_ID_RELATED_PRODUCT => $relatedProductId,
                ]);

                $isRelatedAdded =  $product->related()->save($relatedProduct);
                $isRelatedAdded =  ($isRelatedAdded and $isRelatedAdded->exists);
                $isRelatedAdded ?: S\throwEx(new ValidationException($relatedProduct->getValidator()));
            }

            // Remove
            /** @noinspection PhpUndefinedMethodInspection */
            $toRemove = $product->related()
                ->whereIn(ProductRelated::FIELD_ID_RELATED_PRODUCT, $toRemove)
                ->lists(ProductRelated::FIELD_ID);
            $this->productRelatedModel->destroy($toRemove);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updatedRelated', $product));
    }
}
