<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoicePayment;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class InvoicePaymentRepository extends IndexBasedResourceRepository implements InvoicePaymentRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(InvoicePayment::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Invoice $invoice, array $attributes)
    {
        /** @var InvoicePayment $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $invoice, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(InvoicePayment $resource, Invoice $invoice = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            InvoicePayment::FIELD_ID_INVOICE => $invoice,
        ], $attributes);
    }
}
