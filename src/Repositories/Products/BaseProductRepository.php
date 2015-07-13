<?php namespace Neomerx\Core\Repositories\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\BaseProduct;
use \Neomerx\Core\Models\Manufacturer;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class BaseProductRepository extends CodeBasedResourceRepository implements BaseProductRepositoryInterface
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
        array $attributes
    ) {
        /** @var BaseProduct $base */
        $base = $this->makeModel();
        $this->fill($base, $manufacturer, $attributes);
        return $base;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        BaseProduct $base,
        Manufacturer $manufacturer = null,
        array $attributes = null
    ) {
        $this->fillModel($base, [
            BaseProduct::FIELD_ID_MANUFACTURER     => $manufacturer,
        ], $attributes);
    }
}
