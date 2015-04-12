<?php namespace Neomerx\Core\Repositories\Invoices;

use \Neomerx\Core\Models\Invoice;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface InvoiceRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Invoice
     */
    public function instance(array $attributes);

    /**
     * @param Invoice    $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Invoice $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Invoice
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
