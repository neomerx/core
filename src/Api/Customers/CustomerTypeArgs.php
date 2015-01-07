<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\CustomerType;

class CustomerTypeArgs extends EventArgs
{
    /**
     * @var CustomerType
     */
    private $customerType;

    /**
     * @param string       $name
     * @param CustomerType $customerType
     * @param EventArgs    $args
     */
    public function __construct($name, CustomerType $customerType, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->customerType = $customerType;
    }

    /**
     * @return CustomerType
     */
    public function getModel()
    {
        return $this->customerType;
    }
}
