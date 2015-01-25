<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Repositories\RepositoryInterface;

interface TaxRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Tax
     */
    public function instance(array $attributes);

    /**
     * @param Tax   $resource
     * @param array $attributes
     *
     * @return void
     */
    public function fill(Tax $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Tax
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
