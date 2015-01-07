<?php namespace Neomerx\Core\Api\SupplyOrders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\SupplyOrder;

class SupplyOrderArgs extends EventArgs
{
    /**
     * @var SupplyOrder
     */
    private $supplyOrder;

    /**
     * @param string      $name
     * @param SupplyOrder $supplyOrder
     * @param EventArgs   $args
     */
    public function __construct($name, SupplyOrder $supplyOrder, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->supplyOrder = $supplyOrder;
    }

    /**
     * @return SupplyOrder
     */
    public function getModel()
    {
        return $this->supplyOrder;
    }
}
