<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\ProductTaxType;
use \Illuminate\Database\Eloquent\Collection;

interface ProductTaxTypesInterface extends CrudInterface
{
    /**
     * Create product tax type.
     *
     * @param array $input
     *
     * @return ProductTaxType
     */
    public function create(array $input);

    /**
     * Read product tax by identifier.
     *
     * @param string $code
     *
     * @return ProductTaxType
     */
    public function read($code);

    /**
     * Get all product tax types.
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function all(array $parameters = []);
}
