<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class BaseProductRepository extends IndexBasedResourceRepository implements BaseProductRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(BaseProduct::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Manufacturer $manufacturer,
        Currency $currency,
        array $attributes
    ) {
        /** @var BaseProduct $base */
        $base = $this->makeModel();
        $this->fill($base, $manufacturer, $currency, $attributes);
        return $base;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        BaseProduct $base,
        Manufacturer $manufacturer = null,
        Currency $currency = null,
        array $attributes = null
    ) {
        $this->fillModel($base, [
            BaseProduct::FIELD_ID_CURRENCY     => $currency,
            BaseProduct::FIELD_ID_MANUFACTURER => $manufacturer,
        ], $attributes);
    }
}
