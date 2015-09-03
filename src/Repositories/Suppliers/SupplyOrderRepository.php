<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class SupplyOrderRepository extends BaseRepository implements SupplyOrderRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(SupplyOrder::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(
        Supplier $supplier,
        Warehouse $warehouse,
        Currency $currency,
        Language $language,
        array $attributes
    ) {
        return $this->create(
            $this->idOf($supplier),
            $this->idOf($warehouse),
            $this->idOf($currency),
            $this->idOf($language),
            $attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function create(
        $supplierId,
        $warehouseId,
        $currencyId,
        $languageId,
        array $attributes
    ) {
        $resource = $this->createWith(
            $attributes,
            $this->getRelationships($supplierId, $warehouseId, $currencyId, $languageId)
        );

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        SupplyOrder $resource,
        Supplier $supplier = null,
        Warehouse $warehouse = null,
        Currency $currency = null,
        Language $language = null,
        array $attributes = null
    ) {
        $this->update(
            $resource,
            $this->idOf($supplier),
            $this->idOf($warehouse),
            $this->idOf($currency),
            $this->idOf($language),
            $attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function update(
        SupplyOrder $resource,
        $supplierId = null,
        $warehouseId = null,
        $currencyId = null,
        $languageId = null,
        array $attributes = null
    ) {
        $this->updateWith(
            $resource,
            $attributes,
            $this->getRelationships($supplierId, $warehouseId, $currencyId, $languageId)
        );
    }

    /**
     * @param $supplierId
     * @param $warehouseId
     * @param $currencyId
     * @param $languageId
     *
     * @return array
     */
    protected function getRelationships($supplierId, $warehouseId, $currencyId, $languageId)
    {
        return $this->filterNulls([
            SupplyOrder::FIELD_ID_SUPPLIER  => $supplierId,
            SupplyOrder::FIELD_ID_WAREHOUSE => $warehouseId,
            SupplyOrder::FIELD_ID_CURRENCY  => $currencyId,
            SupplyOrder::FIELD_ID_LANGUAGE  => $languageId,
        ]);
    }
}
