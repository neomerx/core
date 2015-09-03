<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SupplyOrderDetailsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param SupplyOrder $order
     * @param Product     $product
     * @param array       $attributes
     *
     * @return SupplyOrderDetails
     */
    public function createWithObjects(SupplyOrder $order, Product $product, array $attributes);

    /**
     * @param int   $orderId
     * @param int   $productId
     * @param array $attributes
     *
     * @return SupplyOrderDetails
     */
    public function create($orderId, $productId, array $attributes);

    /**
     * @param SupplyOrderDetails $resource
     * @param SupplyOrder|null   $order
     * @param Product|null       $product
     * @param array|null         $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        SupplyOrderDetails $resource,
        SupplyOrder $order = null,
        Product $product = null,
        array $attributes = null
    );

    /**
     * @param SupplyOrderDetails $resource
     * @param int|null           $orderId
     * @param int|null           $productId
     * @param array|null         $attributes
     *
     * @return void
     */
    public function update(
        SupplyOrderDetails $resource,
        $orderId = null,
        $productId = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return SupplyOrder
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
