<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\Specification;

class SpecificationArgs extends EventArgs
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * @param string        $name
     * @param Specification $specification
     * @param EventArgs     $args
     */
    public function __construct($name, Specification $specification, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->specification = $specification;
    }

    /**
     * @return Specification
     */
    public function getModel()
    {
        return $this->specification;
    }
}
