<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Aspect;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Feature;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\FeatureValue;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class AspectRepository extends IndexBasedResourceRepository implements AspectRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Aspect::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        BaseProduct $base,
        FeatureValue $value,
        array $attributes,
        Product $product = null
    ) {
        /** @var Aspect $aspect */
        $aspect = $this->makeModel();
        $this->fill($aspect, $base, $value, $attributes, $product);
        return $aspect;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        Aspect $aspect,
        BaseProduct $base = null,
        FeatureValue $value = null,
        array $attributes = null,
        Product $product = null
    ) {
        $this->fillModel($aspect, [
            Aspect::FIELD_ID_BASE_PRODUCT => $base,
            Aspect::FIELD_ID_PRODUCT      => $product,
            Aspect::FIELD_ID_VALUE        => $value,
        ], $attributes);
    }

    /**
     * @inheritdoc
     */
    public function makeVariable(Aspect $aspect)
    {
        // Basically we want having such aspect for all products. Which requires the following steps
        // 1) Get default product and all other products.
        // 2) Assign aspect to default product.
        // 3) Make copy of aspect for every non-default product.
        // 2-3 should be done in transaction.
        $base     = $aspect->{Aspect::FIELD_BASE_PRODUCT};
        $products = $base->{BaseProduct::FIELD_PRODUCTS};
        $value    = $aspect->value;

        $attributes = $aspect->attributesToArray();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->executeInTransaction(function () use ($products, $aspect, $base, $value, $attributes) {
            foreach ($products as $product) {
                /** @var Product $product */
                if ($product->isDefault() === true) {
                    $aspect->{Aspect::FIELD_ID_PRODUCT} = $product->{Product::FIELD_ID};
                    $aspect->saveOrFail();
                } else {
                    $this->instance($base, $value, $attributes, $product)->saveOrFail();
                }
            }
        });
    }

    /**
     * @inheritdoc
     *
     * @throws \Neomerx\Core\Exceptions\LogicException
     */
    public function makeNonVariable(Aspect $aspect)
    {
        // This method could be called on aspect that belongs to default product.
        // Technically it removes all aspects with the same feature (not value!)
        // from non-default products and moves aspect from default product to base product
        // which is just removing relation between aspect and product.

        $base    = $aspect->{Aspect::FIELD_BASE_PRODUCT};
        $product = $aspect->{Aspect::FIELD_PRODUCT};

        // check we are variable as[ect and belonging to default product
        if ($product === null || $product->isDefault() === false) {
            throw new LogicException();
        }

        $featureId = $aspect->{Aspect::FIELD_VALUE}->{FeatureValue::FIELD_FEATURE}->{Feature::FIELD_ID};
        $this->executeInTransaction(function () use ($aspect, $base, $featureId) {
            // assign aspect back to product
            $aspect->{Aspect::FIELD_ID_PRODUCT} = null;
            $aspect->saveOrFail();

            // remove all other aspects that belong to the same product and same feature
            /** @noinspection PhpUndefinedMethodInspection */
            $specIdsToDelete = DB::table(Aspect::TABLE_NAME)->join(
                // join feature value on its ID
                FeatureValue::TABLE_NAME,
                FeatureValue::TABLE_NAME.'.'.FeatureValue::FIELD_ID,
                '=',
                Aspect::TABLE_NAME.'.'.Aspect::FIELD_ID_VALUE
            )->where(
                // only for current product
                Aspect::TABLE_NAME.'.'.Aspect::FIELD_ID_BASE_PRODUCT,
                $base->{BaseProduct::FIELD_ID}
            )->where(
                // only with the same feature
                FeatureValue::TABLE_NAME.'.'. FeatureValue::FIELD_ID_FEATURE,
                $featureId
            )->whereNotNull(
                // except aspects which belong to product only
                Aspect::TABLE_NAME.'.'.Aspect::FIELD_ID_PRODUCT
            )->lists(Aspect::FIELD_ID);

            if (empty($specIdsToDelete) === false) {
                /** @noinspection PhpUndefinedMethodInspection */
                DB::table(Aspect::TABLE_NAME)->whereIn(Aspect::FIELD_ID, $specIdsToDelete)->delete();
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function getMaxPosition(BaseProduct $base)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $position = $this->getUnderlyingModel()
            ->newQuery()
            ->where(Aspect::FIELD_ID_BASE_PRODUCT, '=', $base->{BaseProduct::FIELD_ID})
            ->max(Aspect::FIELD_POSITION);
        return $position === null ? 0 : (int)$position;
    }
}
