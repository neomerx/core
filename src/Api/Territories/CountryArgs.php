<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Events\EventArgs;

class CountryArgs extends EventArgs
{
    /**
     * @var Country
     */
    private $country;

    /**
     * @param string    $name
     * @param Country   $country
     * @param EventArgs $args
     */
    public function __construct($name, Country $country, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->country = $country;
    }

    /**
     * @return Country
     */
    public function getModel()
    {
        return $this->country;
    }
}
