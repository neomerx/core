<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoiceOrder;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface OrderInvoiceRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order   $order
     * @param Invoice $invoice
     *
     * @return InvoiceOrder
     */
    public function instance(Order $order, Invoice $invoice);

    /**
     * @param InvoiceOrder $resource
     * @param Order|null   $order
     * @param Invoice|null $invoice
     *
     * @return void
     */
    public function fill(InvoiceOrder $resource, Order $order = null, Invoice $invoice = null);

    /**
     * @param int    $resourceId
     * @param array  $scopes
     * @param array  $columns
     *
     * @return InvoiceOrder
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
