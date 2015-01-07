<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Events\EventArgs;

class CustomerArgs extends EventArgs
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @param string    $name
     * @param Customer  $customer
     * @param EventArgs $args
     */
    public function __construct($name, Customer $customer, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->customer = $customer;
    }

    /**
     * @return Customer
     */
    public function getModel()
    {
        return $this->customer;
    }
}
