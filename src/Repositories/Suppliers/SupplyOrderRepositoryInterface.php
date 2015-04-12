<?php namespace Neomerx\Core\Repositories\Suppliers;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface SupplyOrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Supplier  $supplier
     * @param Warehouse $warehouse
     * @param Currency  $currency
     * @param Language  $language
     * @param array     $attributes
     *
     * @return SupplyOrder
     */
    public function instance(
        Supplier $supplier,
        Warehouse $warehouse,
        Currency $currency,
        Language $language,
        array $attributes
    );

    /**
     * @param SupplyOrder    $resource
     * @param Supplier|null  $supplier
     * @param Warehouse|null $warehouse
     * @param Currency|null  $currency
     * @param Language|null  $language
     * @param array|null     $attributes
     *
     * @return void
     */
    public function fill(
        SupplyOrder $resource,
        Supplier $supplier = null,
        Warehouse $warehouse = null,
        Currency $currency = null,
        Language $language = null,
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
