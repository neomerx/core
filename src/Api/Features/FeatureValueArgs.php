<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\CharacteristicValue;

class FeatureValueArgs extends EventArgs
{
    /**
     * @var CharacteristicValue
     */
    private $characteristicValue;

    /**
     * @param string              $name
     * @param CharacteristicValue $characteristicValue
     * @param EventArgs           $args
     */
    public function __construct($name, CharacteristicValue $characteristicValue, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->characteristicValue = $characteristicValue;
    }

    /**
     * @return CharacteristicValue
     */
    public function getModel()
    {
        return $this->characteristicValue;
    }
}
