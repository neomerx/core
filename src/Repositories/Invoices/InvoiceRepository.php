<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class InvoiceRepository extends CodeBasedResourceRepository implements InvoiceRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Invoice::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Invoice $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Invoice $resource, array $attributes = null)
    {
        $this->fillModel($resource, [], $attributes);
    }
}