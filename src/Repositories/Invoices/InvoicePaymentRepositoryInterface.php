<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoicePayment;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface InvoicePaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Invoice $invoice
     * @param array   $attributes
     *
     * @return InvoicePayment
     */
    public function instance(Invoice $invoice, array $attributes);

    /**
     * @param InvoicePayment $resource
     * @param Invoice        $invoice
     * @param array|null     $attributes
     *
     * @return void
     */
    public function fill(InvoicePayment $resource, Invoice $invoice = null, array $attributes = null);

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return InvoicePayment
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
