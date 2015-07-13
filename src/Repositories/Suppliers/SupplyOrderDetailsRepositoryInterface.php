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
    public function instance(SupplyOrder $order, Product $product, array $attributes);

    /**
     * @param SupplyOrderDetails $resource
     * @param SupplyOrder|null   $order
     * @param Product|null       $product
     * @param array|null         $attributes
     *
     * @return void
     */
    public function fill(
        SupplyOrderDetails $resource,
        SupplyOrder $order = null,
        Product $product = null,
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
