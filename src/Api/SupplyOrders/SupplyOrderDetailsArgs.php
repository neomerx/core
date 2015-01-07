<?php namespace Neomerx\Core\Api\SupplyOrders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\SupplyOrderDetails;

class SupplyOrderDetailsArgs extends EventArgs
{
    /**
     * @var SupplyOrderDetails
     */
    private $supplyOrderDetails;

    /**
     * @param string             $name
     * @param SupplyOrderDetails $supplyOrderDetails
     * @param EventArgs          $args
     */
    public function __construct($name, SupplyOrderDetails $supplyOrderDetails, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->supplyOrderDetails = $supplyOrderDetails;
    }

    /**
     * @return SupplyOrderDetails
     */
    public function getModel()
    {
        return $this->supplyOrderDetails;
    }
}
