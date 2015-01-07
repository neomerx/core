<?php namespace Neomerx\Core\Api\Suppliers;

use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface SuppliersInterface extends CrudInterface
{
    const PARAM_ADDRESS    = Supplier::FIELD_ADDRESS;
    const PARAM_PROPERTIES = Supplier::FIELD_PROPERTIES;

    /**
     * Create supplier.
     *
     * @param array $input
     *
     * @return Supplier
     */
    public function create(array $input);

    /**
     * Read supplier by identifier.
     *
     * @param string $code
     *
     * @return Supplier
     */
    public function read($code);
    /**
     * Search suppliers.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function search(array $parameters = []);
}
