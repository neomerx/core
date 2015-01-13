<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Specification;

class VariantRepository extends BaseRepository implements VariantRepositoryInterface
{
    /**
     * @var SpecificationRepositoryInterface $specificationRepository
     */
    private $specificationRepo;

    /**
     * @inheritdoc
     */
    public function __construct(SpecificationRepositoryInterface $specificationRepo)
    {
        parent::__construct(Variant::BIND_NAME);
        $this->specificationRepo = $specificationRepo;
    }

    /**
     * @inheritdoc
     */
    public function instance(Product $product, array $attributes = null)
    {
        /** @var \Neomerx\Core\Models\Variant $variant */
        $variant = $this->makeModel();
        $this->fill($variant, $product, $attributes);
        return $variant;
    }

    /**
     * @inheritdoc
     */
    public function fill(Variant $variant, Product $product = null, array $attributes = null)
    {
        /** @var Product $product */
        $product === null ?: $variant->setAttribute(Variant::FIELD_SKU, $product->getAttribute(Product::FIELD_SKU));
        $this->fillModel($variant, [
            Variant::FIELD_ID_PRODUCT => $product,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create(Product $product, array $attributes = null)
    {
        $defaultVariant = $product->getDefaultVariant();
        // for just created products there is no default variant yet
        $defaultSpecs = $defaultVariant !== null ? $defaultVariant->specification : [];

        $variant = $this->instance($product, $attributes);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $variant->saveOrFail();
            foreach ($defaultSpecs as $specRow) {
                /** @var Specification $specRow */
                $this->specificationRepo
                    ->instance($product, $specRow->value, $variant, $specRow->attributesToArray())
                    ->saveOrFail();
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }

        return $variant;
    }
}
