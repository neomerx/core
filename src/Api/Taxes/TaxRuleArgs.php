<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Events\EventArgs;

class TaxRuleArgs extends EventArgs
{
    /**
     * @var TaxRule
     */
    private $taxRule;

    /**
     * @param string    $name
     * @param TaxRule   $taxRule
     * @param EventArgs $args
     */
    public function __construct($name, TaxRule $taxRule, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->taxRule = $taxRule;
    }

    /**
     * @return TaxRule
     */
    public function getModel()
    {
        return $this->taxRule;
    }
}
