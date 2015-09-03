<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoicePayment;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class InvoicePaymentRepository extends BaseRepository implements InvoicePaymentRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(InvoicePayment::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Invoice $invoice, array $attributes)
    {
        return $this->create($this->idOf($invoice), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($invoiceId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($invoiceId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(InvoicePayment $resource, Invoice $invoice = null, array $attributes = null)
    {
        $this->update($resource, $this->idOf($invoice), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(InvoicePayment $resource, $invoiceId = null, array $attributes = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($invoiceId));
    }

    /**
     * @param int $invoiceId
     *
     * @return array
     */
    protected function getRelationships($invoiceId)
    {
        return $this->filterNulls([
            InvoicePayment::FIELD_ID_INVOICE => $invoiceId,
        ]);
    }
}
