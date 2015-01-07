<?php namespace Neomerx\Core\Api\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface CurrenciesInterface extends CrudInterface
{
    const PARAM_PROPERTIES = Currency::FIELD_PROPERTIES;

    /**
     * Create currency.
     *
     * @param array $input
     *
     * @return Currency
     */
    public function create(array $input);

    /**
     * Read currency by identifier.
     *
     * @param string $code
     *
     * @return Currency
     */
    public function read($code);

    /**
     * Get all currencies in the system.
     *
     * @return Collection
     */
    public function all();
}
