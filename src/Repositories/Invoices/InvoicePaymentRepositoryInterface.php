<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoicePayment;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface InvoicePaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Invoice $invoice
     * @param array   $attributes
     *
     * @return InvoicePayment
     */
    public function createWithObjects(Invoice $invoice, array $attributes);

    /**
     * @param int   $invoiceId
     * @param array $attributes
     *
     * @return InvoicePayment
     */
    public function create($invoiceId, array $attributes);

    /**
     * @param InvoicePayment $resource
     * @param Invoice|null   $invoice
     * @param array|null     $attributes
     *
     * @return void
     */
    public function updateWithObjects(InvoicePayment $resource, Invoice $invoice = null, array $attributes = null);

    /**
     * @param InvoicePayment $resource
     * @param int|null       $invoiceId
     * @param array|null     $attributes
     *
     * @return void
     */
    public function update(InvoicePayment $resource, $invoiceId = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return InvoicePayment
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
