<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class SupplyOrderRepository extends IndexBasedResourceRepository implements SupplyOrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplyOrder::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(
        Supplier $supplier,
        Warehouse $warehouse,
        Currency $currency,
        Language $language,
        array $attributes
    ) {
        /** @var SupplyOrder $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $supplier, $warehouse, $currency, $language, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        SupplyOrder $resource,
        Supplier $supplier = null,
        Warehouse $warehouse = null,
        Currency $currency = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->fillModel($resource, [
            SupplyOrder::FIELD_ID_SUPPLIER  => $supplier,
            SupplyOrder::FIELD_ID_WAREHOUSE => $warehouse,
            SupplyOrder::FIELD_ID_CURRENCY  => $currency,
            SupplyOrder::FIELD_ID_LANGUAGE  => $language,
        ], $attributes);
    }
}
