<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\Characteristic;

class FeatureCharacteristicArgs extends EventArgs
{
    /**
     * @var Characteristic
     */
    private $characteristic;

    /**
     * @param string         $name
     * @param Characteristic $characteristic
     * @param EventArgs      $args
     */
    public function __construct($name, Characteristic $characteristic, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->characteristic = $characteristic;
    }

    /**
     * @return Characteristic
     */
    public function getModel()
    {
        return $this->characteristic;
    }
}
