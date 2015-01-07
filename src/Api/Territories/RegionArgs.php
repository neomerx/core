<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Events\EventArgs;

class RegionArgs extends EventArgs
{
    /**
     * @var Region
     */
    private $region;

    /**
     * @param string    $name
     * @param Region    $region
     * @param EventArgs $args
     */
    public function __construct($name, Region $region, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->region = $region;
    }

    /**
     * @return Region
     */
    public function getModel()
    {
        return $this->region;
    }
}
