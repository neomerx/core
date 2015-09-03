<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\OrderDetails;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderDetailsRepository extends BaseRepository implements OrderDetailsRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(OrderDetails::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(
        Order $order,
        Product $product,
        array $attributes,
        Nullable $shippingOrder = null
    ) {
        return $this->create(
            $this->idOf($order),
            $this->idOf($product),
            $attributes,
            $this->idOfNullable($shippingOrder, ShippingOrder::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function create(
        $orderId,
        $productId,
        array $attributes,
        Nullable $shippingOrderId = null
    ) {
        $resource = $this->createWith($attributes, $this->getRelationships($orderId, $productId, $shippingOrderId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        OrderDetails $details,
        Order $order = null,
        Product $product = null,
        array $attributes = null,
        Nullable $shippingOrder = null
    ) {
        $this->update(
            $details,
            $this->idOf($order),
            $this->idOf($product),
            $attributes,
            $this->idOfNullable($shippingOrder, ShippingOrder::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function update(
        OrderDetails $details,
        $orderId = null,
        $productId = null,
        array $attributes = null,
        Nullable $shippingOrderId = null
    ) {
        $this->updateWith($details, $attributes, $this->getRelationships($orderId, $productId, $shippingOrderId));
    }

    /**
     * @param int|null      $orderId
     * @param int|null      $productId
     * @param Nullable|null $shippingOrderId
     *
     * @return array
     */
    protected function getRelationships($orderId = null, $productId = null, Nullable $shippingOrderId = null)
    {
        return $this->filterNulls([
            OrderDetails::FIELD_ID_ORDER   => $orderId,
            OrderDetails::FIELD_ID_PRODUCT => $productId,
        ], [
            OrderDetails::FIELD_ID_SHIPPING_ORDER => $shippingOrderId,
        ]);
    }
}
