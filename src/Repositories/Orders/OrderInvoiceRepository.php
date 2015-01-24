<?php namespace Neomerx\Core\Repositories\Orders;

use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Models\InvoiceOrder;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class OrderInvoiceRepository extends IndexBasedResourceRepository implements OrderInvoiceRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(InvoiceOrder::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Order $order, Invoice $invoice)
    {
        /** @var InvoiceOrder $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $order, $invoice);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(InvoiceOrder $resource, Order $order = null, Invoice $invoice = null)
    {
        $this->fillModel($resource, [
            InvoiceOrder::FIELD_ID_ORDER   => $order,
            InvoiceOrder::FIELD_ID_INVOICE => $invoice,
        ]);
    }
}
