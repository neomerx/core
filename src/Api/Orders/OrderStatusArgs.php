<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\OrderStatus;

class OrderStatusArgs extends EventArgs
{
    /**
     * @var OrderStatus
     */
    private $orderStatus;

    /**
     * @param string      $name
     * @param OrderStatus $orderStatus
     * @param EventArgs   $args
     */
    public function __construct($name, OrderStatus $orderStatus, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return OrderStatus
     */
    public function getModel()
    {
        return $this->orderStatus;
    }
}
