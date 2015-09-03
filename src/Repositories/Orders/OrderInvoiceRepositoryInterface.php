<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoiceOrder;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface OrderInvoiceRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Order   $order
     * @param Invoice $invoice
     *
     * @return InvoiceOrder
     */
    public function createWithObjects(Order $order, Invoice $invoice);

    /**
     * @param int $orderId
     * @param int $invoiceId
     *
     * @return InvoiceOrder
     */
    public function create($orderId, $invoiceId);

    /**
     * @param InvoiceOrder $resource
     * @param Order|null   $order
     * @param Invoice|null $invoice
     *
     * @return void
     */
    public function updateWithObjects(InvoiceOrder $resource, Order $order = null, Invoice $invoice = null);

    /**
     * @param InvoiceOrder $resource
     * @param int|null     $orderId
     * @param int|null     $invoiceId
     *
     * @return void
     */
    public function update(InvoiceOrder $resource, $orderId = null, $invoiceId = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return InvoiceOrder
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
