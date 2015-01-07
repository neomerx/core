<?php namespace Neomerx\Core\Api\Cart;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Illuminate\Support\Collection;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class Cart extends Collection
{
    /**
     * @var float Price without tax of all cart items.
     */
    private $priceWoTax = 0.0;

    /**
     * @var float Weight of all cart items.
     */
    private $weight = 0.0;

    /**
     * @var int Quantity of all cart items.
     */
    private $quantity = 0;

    /**
     * float Max item dimension (width, height, length).
     */
    private $maxDimension = 0.0;

    /**
     * @return float
     */
    public function getPriceWoTax()
    {
        return $this->priceWoTax;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getMaxDimension()
    {
        return $this->maxDimension;
    }

    /**
     * @param int $index
     *
     * @return void
     */
    public function forget($index)
    {
        $item = $this->get($index);
        parent::forget($index);
        $item === null ?: $this->correctCollectionOnRemove($item);
    }

    /**
     * @param CartItem $item
     *
     * @return void
     */
    public function push($item)
    {
        $item instanceof CartItem ?: S\throwEx(new InvalidArgumentException('item'));
        parent::push($item);
        $this->correctCollectionOnAdd($item);
    }

    /**
     * @param int      $index
     * @param CartItem $item
     *
     * @return void
     */
    public function put($index, $item)
    {
        $item instanceof CartItem ?: S\throwEx(new InvalidArgumentException('item'));

        $oldItem = $this->get($index);
        $oldItem === null ?: $this->correctCollectionOnRemove($oldItem);

        parent::put($index, $item);

        $this->correctCollectionOnAdd($item);
    }

    /**
     * @param int      $index
     * @param CartItem $item
     *
     * @return void
     */
    public function offsetSet($index, $item)
    {
        $item instanceof CartItem ?: S\throwEx(new InvalidArgumentException('item'));

        $oldItem = $this->get($index);
        $oldItem === null ?: $this->correctCollectionOnRemove($oldItem);

        parent::offsetSet($index, $item);

        $this->correctCollectionOnAdd($item);
    }

    /**
     * @param int $index
     *
     * @return void
     */
    public function offsetUnset($index)
    {
        $oldItem = $this->get($index);
        parent::offsetUnset($index);
        $oldItem === null ?: $this->correctCollectionOnRemove($oldItem);
    }

    /**
     * @param CartItem $item
     *
     * @return void
     */
    private function correctCollectionOnAdd(CartItem $item)
    {
        $product = $item->getVariant()->product;

        $qty = $item->getQuantity();

        $this->priceWoTax  += $item->getVariant()->price_wo_tax  * $qty;
        $this->weight      += $product->pkg_weight * $qty;
        $this->quantity    += $qty;
        $this->maxDimension = max($this->maxDimension, $this->getPkgMaxDimension($product));
    }

    /**
     * @param CartItem $item
     *
     * @return void
     */
    private function correctCollectionOnRemove(CartItem $item)
    {
        $product = $item->getVariant()->product;
        // Variant must have associated product
        $product !== null ?: S\throwEx(new InvalidArgumentException('item'));

        $qty = $item->getQuantity();

        $this->priceWoTax -= $item->getVariant()->price_wo_tax * $qty;
        $this->weight     -= $product->pkg_weight * $qty;
        $this->quantity   -= $item->getQuantity();

        if ($this->getPkgMaxDimension($product) >= $this->maxDimension) {
            // recalculate max dimension
            $curMaxDim = 0;
            /** @var CartItem $cartItem */
            foreach ($this->items as $cartItem) {
                $curMaxDim = max(
                    $curMaxDim,
                    $this->getPkgMaxDimension($cartItem->getVariant()->product)
                );
            }
            $this->maxDimension = $curMaxDim;
        }
    }

    /**
     * @param Product $product
     *
     * @return float
     */
    private function getPkgMaxDimension(Product $product)
    {
        return (float)max($product->pkg_width, $product->pkg_height, $product->pkg_length);
    }
}
