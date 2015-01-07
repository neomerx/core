<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\CustomerType;
use \Illuminate\Database\Eloquent\Collection;

interface CustomerTypesInterface extends CrudInterface
{
    /**
     * Create customer type.
     *
     * @param array $input
     *
     * @return CustomerType
     */
    public function create(array $input);

    /**
     * Read customer type by identifier.
     *
     * @param string $code
     *
     * @return CustomerType
     */
    public function read($code);

    /**
     * Get all customer types.
     *
     * @return Collection
     */
    public function all();
}
