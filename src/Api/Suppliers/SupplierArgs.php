<?php namespace Neomerx\Core\Api\Suppliers;

use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Events\EventArgs;

class SupplierArgs extends EventArgs
{
    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @param string    $name
     * @param Supplier  $supplier
     * @param EventArgs $args
     */
    public function __construct($name, Supplier $supplier, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->supplier = $supplier;
    }

    /**
     * @return Supplier
     */
    public function getModel()
    {
        return $this->supplier;
    }
}
