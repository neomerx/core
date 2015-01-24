<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface SupplyOrderDetailsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param SupplyOrder $order
     * @param Variant     $variant
     * @param array       $attributes
     *
     * @return SupplyOrderDetails
     */
    public function instance(SupplyOrder $order, Variant $variant, array $attributes);

    /**
     * @param SupplyOrderDetails $resource
     * @param SupplyOrder|null   $order
     * @param Variant|null       $variant
     * @param array|null         $attributes
     *
     * @return void
     */
    public function fill(
        SupplyOrderDetails $resource,
        SupplyOrder $order = null,
        Variant $variant = null,
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
