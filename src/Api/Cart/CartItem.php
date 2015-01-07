<?php namespace Neomerx\Core\Api\Cart;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class CartItem
{
    /**
     * @var Variant
     */
    private $variant;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param Variant   $variant
     * @param           $quantity
     */
    public function __construct(Variant $variant, $quantity)
    {
        settype($quantity, 'int');
        $quantity > 0 ?: S\throwEx(new InvalidArgumentException('quantity'));

        $this->variant   = $variant;
        $this->quantity  = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return Variant
     */
    public function getVariant()
    {
        return $this->variant;
    }
}
