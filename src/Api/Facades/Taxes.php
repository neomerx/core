<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Models\CustomerType;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Api\Taxes\TaxesInterface;
use \Neomerx\Core\Api\Taxes\TaxCalculation;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @see TaxesInterface
 *
 * @method static Tax            create(array $input)
 * @method static Tax            read(string $code)
 * @method static void           update(string $code, array $input)
 * @method static void           delete(string $code)
 * @method static Collection     all()
 * @method static Collection     getTaxes(Address $address, CustomerType $customerType, ProductTaxType $productTaxType)
 * @method static TaxCalculation calculateTax(ShippingData $shippingData, Tariff $shipping)
 */
class Taxes extends Facade
{
    const INTERFACE_BIND_NAME = TaxesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
