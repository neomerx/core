<?php namespace Neomerx\Core\Api\SupplyOrders;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Illuminate\Database\Eloquent\Collection;

interface SupplyOrdersInterface extends CrudInterface
{
    const PARAM_SUPPLIER_CODE  = 'supplier_code';
    const PARAM_WAREHOUSE_CODE = 'warehouse_code';
    const PARAM_CURRENCY_CODE  = 'currency_code';
    const PARAM_LANGUAGE_CODE  = 'language_code';
    const PARAM_EXPECTED_AT    = SupplyOrder::FIELD_EXPECTED_AT;
    const PARAM_STATUS         = SupplyOrder::FIELD_STATUS;
    const PARAM_DETAILS        = SupplyOrder::FIELD_DETAILS;
    const PARAM_DETAILS_SKU    = 'variant_sku';

    /**
     * Create supply order.
     *
     * @param array $input
     *
     * @return SupplyOrder
     */
    public function create(array $input);

    /**
     * Read supply order by identifier.
     *
     * @param int $supplyOrderId
     *
     * @return SupplyOrder
     */
    public function read($supplyOrderId);

    /**
     * Update supply order.
     *
     * @param int   $supplyOrderId
     * @param array $input
     *
     * @return void
     */
    public function update($supplyOrderId, array $input);

    /**
     * Delete supply order.
     *
     * @param int $supplyOrderId
     *
     * @return void
     */
    public function delete($supplyOrderId);

    /**
     * Create supply order details.
     *
     * @param SupplyOrder $supplyOrder
     * @param array       $input
     *
     * @return SupplyOrderDetails
     */
    public function createDetails(SupplyOrder $supplyOrder, array $input);

    /**
     * Read supply order details.
     *
     * @param int $detailsId
     *
     * @return SupplyOrderDetails
     */
    public function readDetails($detailsId);

    /**
     * Update supply order details.
     *
     * @param SupplyOrderDetails $detailsRow
     * @param array              $input
     *
     * @return void
     */
    public function updateDetails(SupplyOrderDetails $detailsRow, array $input);

    /**
     * Delete supply order details.
     *
     * @param int $detailsId
     *
     * @return void
     */
    public function deleteDetails($detailsId);

    /**
     * Search supply orders.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
