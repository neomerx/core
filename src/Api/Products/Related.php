<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Models\ProductRelated as ProductRelatedModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Related
{
    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var ProductRelatedModel
     */
    private $productRelatedModel;

    /**
     * @var array
     */
    private $relations;

    /**
     * @param Model               $product
     * @param ProductRelatedModel $productRelated
     * @param array               $modelRelations
     */
    public function __construct(Model $product, ProductRelatedModel $productRelated, array $modelRelations)
    {
        $this->productModel        = $product;
        $this->productRelatedModel = $productRelated;
        $this->relations           = $modelRelations;
    }

    /**
     * Read related products.
     *
     * @param Model $product
     *
     * @return Collection
     */
    public function showRelated(Model $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $related = $product->relatedProducts()->with($this->relations)->get();
        return $related;
    }

    /**
     * Set related products to product.
     *
     * @param Model $product
     * @param array $productSKUs
     *
     * @return void
     */
    public function updateRelated(Model $product, array $productSKUs)
    {
        Permissions::check($product, Permission::edit());

        $productIds  = $this->productModel->selectByCodes($productSKUs)->lists(Model::FIELD_ID);
        /** @noinspection PhpUndefinedMethodInspection */
        $relatedIds  = $product->related()->lists('id_related_product');
        $toAdd       = array_diff($productIds, $relatedIds);
        $toRemove    = array_diff($relatedIds, $productIds);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // Add
            foreach ($toAdd as $relatedProductId) {
                /** @var ProductRelatedModel $relatedProduct */
                /** @noinspection PhpUndefinedMethodInspection */
                $relatedProduct = App::make(ProductRelatedModel::BIND_NAME);
                $relatedProduct->fill([
                    ProductRelatedModel::FIELD_ID_RELATED_PRODUCT => $relatedProductId,
                ]);

                $isRelatedAdded =  $product->related()->save($relatedProduct);
                $isRelatedAdded =  ($isRelatedAdded and $isRelatedAdded->exists);
                $isRelatedAdded ?: S\throwEx(new ValidationException($relatedProduct->getValidator()));
            }

            // Remove
            /** @noinspection PhpUndefinedMethodInspection */
            $toRemove = $product->related()
                ->whereIn('id_related_product', $toRemove)
                ->lists(ProductRelatedModel::FIELD_ID);
            $this->productRelatedModel->destroy($toRemove);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updatedRelated', $product));
    }
}
