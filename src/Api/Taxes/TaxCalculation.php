<?php namespace Neomerx\Core\Api\Taxes;

class TaxCalculation
{
    /**
     * @var float
     */
    private $tax;

    /**
     * @var array
     */
    private $details;

    /**
     * @var float
     */
    private $shippingCost;

    /**
     * @param float $tax
     * @param array $details
     * @param float $shippingCost
     */
    public function __construct($tax, array $details, $shippingCost)
    {
        $this->tax          = $tax;
        $this->details      = $details;
        $this->shippingCost = $shippingCost;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return float
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }
}
