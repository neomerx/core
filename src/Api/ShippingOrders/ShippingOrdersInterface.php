<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Illuminate\Database\Eloquent\Collection;

interface ShippingOrdersInterface extends CrudInterface
{
    const PARAM_ID_ORDER        = Order::FIELD_ID;
    const PARAM_DETAIL_IDS      = Order::FIELD_DETAILS;
    const PARAM_CARRIER_CODE    = 'carrier_code';
    const PARAM_TRACKING_NUMBER = ShippingOrder::FIELD_TRACKING_NUMBER;
    const PARAM_STATUS_CODE     = 'status_code';

    /**
     * Create shipping order.
     *
     * @param array $input
     *
     * @return ShippingOrder
     */
    public function create(array $input);

    /**
     * Read shipping order by identifier.
     *
     * @param int $shippingOrderId
     *
     * @return ShippingOrder
     */
    public function read($shippingOrderId);

    /**
     * Update shipping order.
     *
     * @param int   $shippingOrderId
     * @param array $input
     *
     * @return void
     */
    public function update($shippingOrderId, array $input);

    /**
     * Delete shipping order by identifier.
     *
     * @param int $shippingOrderId
     *
     * @return void
     */
    public function delete($shippingOrderId);

    /**
     * Search shipping orders.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);

    /**
     * Calculate shipping costs and taxes.
     *
     * @param ShippingData $shippingData
     * @param Carrier      $carrier
     *
     * @return Tariff
     */
    public function calculateCosts(ShippingData $shippingData, Carrier $carrier);
}
