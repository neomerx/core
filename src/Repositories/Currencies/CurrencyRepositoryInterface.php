<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CurrencyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Currency
     */
    public function instance(array $attributes);

    /**
     * @param Currency $resource
     * @param array    $attributes
     *
     * @return void
     */
    public function fill(Currency $resource, array $attributes);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Currency
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
