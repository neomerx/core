<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Api\CrudInterface;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Illuminate\Database\Eloquent\Collection;

interface TaxesInterface extends CrudInterface
{
    /**
     * Create tax.
     *
     * @param array $input
     *
     * @return Tax
     */
    public function create(array $input);

    /**
     * Read tax by identifier.
     *
     * @param string $code
     *
     * @return Tax
     */
    public function read($code);

    /**
     * Get all taxes in the system.
     *
     * @return Collection
     */
    public function all();

    /**
     * Get taxes.
     *
     * @param Address        $address
     * @param CustomerType   $customerType
     * @param ProductTaxType $productTaxType
     *
     * @return Collection
     */
    public function getTaxes(Address $address, CustomerType $customerType, ProductTaxType $productTaxType);

    /**
     * Calculate tax rate.
     *
     * @param ShippingData $shippingData
     * @param Tariff       $shipping
     *
     * @return TaxCalculation
     */
    public function calculateTax(ShippingData $shippingData, Tariff $shipping);
}
