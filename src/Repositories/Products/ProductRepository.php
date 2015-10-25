<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Models\Aspect;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * @var AspectRepositoryInterface
     */
    private $aspectRepo;

    /**
     * @var BaseProductRepositoryInterface
     */
    private $baseProductRepo;

    /**
     * @inheritdoc
     */
    public function __construct(AspectRepositoryInterface $aspectRepo, BaseProductRepositoryInterface $baseProductRepo)
    {
        parent::__construct(Product::class);

        $this->aspectRepo      = $aspectRepo;
        $this->baseProductRepo = $baseProductRepo;
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(
        BaseProduct $baseProduct,
        Category $category,
        ProductTaxType $taxType,
        array $attributes
    ) {
        $product = $this->createProduct($baseProduct, $this->idOf($category), $this->idOf($taxType), $attributes);

        return $product;
    }

    /**
     * @inheritdoc
     */
    public function create($baseProductId, $categoryId, $taxTypeId, array $attributes)
    {
        $baseProduct = $this->baseProductRepo->read($baseProductId, [BaseProduct::withProductAspectValues()]);
        $product = $this->createProduct($baseProduct, $categoryId, $taxTypeId, $attributes);

        return $product;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        Product $product,
        Category $category = null,
        ProductTaxType $taxType = null,
        array $attributes = null
    ) {
        $this->update($product, $this->idOf($category), $this->idOf($taxType), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        Product $product,
        $categoryId = null,
        $taxTypeId = null,
        array $attributes = null
    ) {
        $this->updateWith($product, $attributes, $this->filterNulls([
            Product::FIELD_ID_CATEGORY_DEFAULT => $categoryId,
            Product::FIELD_ID_PRODUCT_TAX_TYPE => $taxTypeId,
        ]));
    }

    /**
     * @param BaseProduct $baseProduct
     * @param int         $categoryId
     * @param int         $taxTypeId
     * @param array       $attributes
     *
     * @return Product
     */
    private function createProduct(
        BaseProduct $baseProduct,
        $categoryId,
        $taxTypeId,
        array $attributes
    ) {
        $defaultProduct = $baseProduct->getDefaultProduct();
        // for just created base products there is no default product yet
        $defaultAspects = $defaultProduct !== null ? $defaultProduct->{Product::FIELD_ASPECTS} : [];

        $product = null;
        $baseProductId = $this->idOf($baseProduct);
        $this->executeInTransaction(function () use (
            &$product,
            $defaultAspects,
            $baseProductId,
            $categoryId,
            $taxTypeId,
            $attributes
        ) {
            $product = $this->createWith($attributes, [
                Product::FIELD_ID_BASE_PRODUCT     => $baseProductId,
                Product::FIELD_ID_CATEGORY_DEFAULT => $categoryId,
                Product::FIELD_ID_PRODUCT_TAX_TYPE => $taxTypeId,
            ]);
            $productId = $this->idOfNullable($this->getNullable($product), Product::class);
            foreach ($defaultAspects as $aspect) {
                /** @var Aspect $aspect */
                if ($aspect->{Aspect::FIELD_IS_SHARED} === false) {
                    $valueId = $this->idOf($aspect->{Aspect::FIELD_VALUE});
                    $this->aspectRepo->create($baseProductId, $valueId, $aspect->attributesToArray(), $productId);
                }
            }
        });

        return $product;
    }
}
