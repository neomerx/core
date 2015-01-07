<?php namespace Neomerx\Core\Api\ShippingOrders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\ShippingOrder;

class ShippingOrderArgs extends EventArgs
{
    /**
     * @var ShippingOrder
     */
    private $shippingOrder;

    /**
     * @param string        $name
     * @param ShippingOrder $shippingOrder
     * @param EventArgs     $args
     */
    public function __construct($name, ShippingOrder $shippingOrder, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->shippingOrder = $shippingOrder;
    }

    /**
     * @return ShippingOrder
     */
    public function getModel()
    {
        return $this->shippingOrder;
    }
}
