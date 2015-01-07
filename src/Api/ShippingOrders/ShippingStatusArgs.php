<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\ShippingOrderStatus;

class ShippingStatusArgs extends EventArgs
{
    /**
     * @var ShippingOrderStatus
     */
    private $shippingOrderStatus;

    /**
     * @param string              $name
     * @param ShippingOrderStatus $shippingOrderStatus
     * @param EventArgs           $args
     */
    public function __construct($name, ShippingOrderStatus $shippingOrderStatus, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->shippingOrderStatus = $shippingOrderStatus;
    }

    /**
     * @return ShippingOrderStatus
     */
    public function getModel()
    {
        return $this->shippingOrderStatus;
    }
}
