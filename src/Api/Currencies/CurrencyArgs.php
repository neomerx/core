<?php namespace Neomerx\Core\Api\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Events\EventArgs;

class CurrencyArgs extends EventArgs
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @param string    $name
     * @param Currency  $currency
     * @param EventArgs $args
     */
    public function __construct($name, Currency $currency, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->currency = $currency;
    }

    /**
     * @return Currency
     */
    public function getModel()
    {
        return $this->currency;
    }
}
