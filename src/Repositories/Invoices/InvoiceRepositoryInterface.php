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
    public function create(array $attributes);

    /**
     * @param Invoice    $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function update(Invoice $resource, array $attributes);

    /**
     * @param int    $index
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Invoice
     */
    public function read($index, array $scopes = [], array $columns = ['*']);
}
