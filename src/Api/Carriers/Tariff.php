<?php namespace Neomerx\Core\Api\Carriers;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class Tariff
{
    /**
     * @var float
     */
    private $cost;

    /**
     * @param float $cost
     */
    public function __construct($cost)
    {
        $cost >= 0 ?: S\throwEx(new InvalidArgumentException('cost'));
        (isset($includedTax) and $includedTax < 0) ? S\throwEx(new InvalidArgumentException('includedTax')) : null;

        $this->cost = $cost;
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }
}
