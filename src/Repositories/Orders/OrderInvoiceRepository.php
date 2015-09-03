<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoiceOrder;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class OrderInvoiceRepository extends BaseRepository implements OrderInvoiceRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(InvoiceOrder::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Order $order, Invoice $invoice)
    {
        return $this->create($this->idOf($order), $this->idOf($invoice));
    }

    /**
     * @inheritdoc
     */
    public function create($orderId, $invoiceId)
    {
        $resource = $this->createWith([], $this->getRelationships($orderId, $invoiceId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(InvoiceOrder $resource, Order $order = null, Invoice $invoice = null)
    {
        $this->update($resource, $this->idOf($order), $this->idOf($invoice));
    }

    /**
     * @inheritdoc
     */
    public function update(InvoiceOrder $resource, $orderId = null, $invoiceId = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($orderId, $invoiceId));
    }

    /**
     * @param int $orderId
     * @param int $invoiceId
     *
     * @return array
     */
    private function getRelationships($orderId, $invoiceId)
    {
        return $this->filterNulls([
            InvoiceOrder::FIELD_ID_ORDER   => $orderId,
            InvoiceOrder::FIELD_ID_INVOICE => $invoiceId,
        ]);
    }
}
