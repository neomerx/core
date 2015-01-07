<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Config;
use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Cart\CartItem;
use \Neomerx\Core\Api\Carriers\Tariff;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Carriers\ShippingData;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Converters\ConverterInterface;
use \Neomerx\Core\Converters\VariantConverterGeneric;
use \Neomerx\Core\Converters\AddressConverterGeneric;
use \Neomerx\Core\Converters\CustomerConverterGeneric;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Taxes implements TaxesInterface
{
    const EVENT_PREFIX = 'Api.Tax.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Tax
     */
    private $taxModel;

    /**
     * @var Address
     */
    private $addressModel;

    /**
     * @var Customer
     */
    private $customerModel;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @param Tax      $taxModel
     * @param Address  $addressModel
     * @param Customer $customerModel
     * @param Variant  $variantModel
     */
    public function __construct(Tax $taxModel, Address $addressModel, Customer $customerModel, Variant $variantModel)
    {
        $this->taxModel      = $taxModel;
        $this->addressModel  = $addressModel;
        $this->customerModel = $customerModel;
        $this->variantModel  = $variantModel;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Tax $tax */
            $tax = $this->taxModel->createOrFailResource($input);
            Permissions::check($tax, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new TaxArgs(self::EVENT_PREFIX . 'created', $tax));

        return $tax;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Tax $tax */
        $tax = $this->taxModel->selectByCode($code)->firstOrFail();
        Permissions::check($tax, Permission::view());
        return $tax;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var Tax $tax */
        $tax = $this->taxModel->selectByCode($code)->firstOrFail();
        Permissions::check($tax, Permission::edit());
        empty($input) ?: $tax->updateOrFail($input);

        Event::fire(new TaxArgs(self::EVENT_PREFIX . 'updated', $tax));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Tax $tax */
        $tax = $this->taxModel->selectByCode($code)->firstOrFail();
        Permissions::check($tax, Permission::delete());
        $tax->deleteOrFail();

        Event::fire(new TaxArgs(self::EVENT_PREFIX . 'deleted', $tax));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $taxes = $this->taxModel->all();

        foreach ($taxes as $tax) {
            Permissions::check($tax, Permission::view());
        }

        return $taxes;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes(Address $address, CustomerType $customerType, ProductTaxType $productTaxType)
    {
        $region = $address->{Address::FIELD_REGION};
        return $this->taxModel->selectTaxes(
            $region->{Country::FIELD_ID},
            $region->{Region::FIELD_ID},
            $address->postcode,
            $customerType->{CustomerType::FIELD_ID},
            $productTaxType->{ProductTaxType::FIELD_ID}
        );
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(ShippingData $shippingData, Tariff $shipping)
    {
        $addressTo = (ShippingData::TYPE_PICKUP === $shippingData->getShippingType() ?
            $shippingData->getPickupStore()->address : $shippingData->getAddressTo());

        $taxAddress = Config::get(Config::FILE_APP, Config::KEY_TAX_ADDRESS_USE_FROM_INSTEADOF_TO) ?
            $shippingData->getAddressFrom() : $addressTo;

        $customer       = $shippingData->getCustomer();
        $countryId      = $taxAddress->region->{Region::FIELD_ID_COUNTRY};
        $regionId       = $taxAddress->{Address::FIELD_ID_REGION};
        $postcode       = $taxAddress->{Address::FIELD_POSTCODE};
        $customerTypeId = $customer->{Customer::FIELD_ID_CUSTOMER_TYPE};

        $taxDetails      = [];
        $totalOrderTaxes = 0;
        $addressFrom     = $shippingData->getAddressFrom();

        // format objects for tax calculation formula
        /** @noinspection PhpUndefinedMethodInspection */
        $taxParameters   = [
            Tax::PARAM_CUSTOMER     => (object)App::make(CustomerConverterGeneric::BIND_NAME)->convert($customer),
            Tax::PARAM_ADDRESS_TO   => (object)App::make(AddressConverterGeneric::BIND_NAME)->convert($addressTo),
            Tax::PARAM_ADDRESS_FROM => (object)App::make(AddressConverterGeneric::BIND_NAME)->convert($addressFrom),
        ];

        /** @var ConverterInterface $variantConverter */
        /** @noinspection PhpUndefinedMethodInspection */
        $variantConverter  = App::make(VariantConverterGeneric::BIND_NAME);

        /** @var CartItem $cartItem */
        foreach ($shippingData->getCart() as $cartItem) {

            $currentVariant = $cartItem->getVariant();

            $taxes = $this->taxModel->selectTaxes(
                $countryId,
                $regionId,
                $postcode,
                $customerTypeId,
                $currentVariant->product->{ProductTaxType::FIELD_ID}
            );

            $this->sumTaxes($taxes, array_merge($taxParameters, [
                Tax::PARAM_PRODUCT  => (object)$variantConverter->convert($currentVariant),
                Tax::PARAM_PRICE    => $currentVariant->price_wo_tax,
                Tax::PARAM_QUANTITY => $cartItem->getQuantity(),
            ]), $totalOrderTaxes, $taxDetails);
        }

        // calculate shipping cost
        $shippingCost = $shipping->getCost();
        $shippingTaxes = $this->taxModel->selectTaxes(
            $countryId,
            $regionId,
            $postcode,
            $customerTypeId,
            Config::get(Config::FILE_APP, Config::KEY_SHIPPING_TAX_TYPE_ID)
        );
        $this->sumTaxes($shippingTaxes, array_merge($taxParameters, [
            Tax::PARAM_PRICE    => $shippingCost,
            Tax::PARAM_QUANTITY => 1,
        ]), $totalOrderTaxes, $taxDetails);

        return new TaxCalculation($totalOrderTaxes, array_values($taxDetails), $shippingCost);
    }

    private function sumTaxes(Collection $taxes, array $parameters, &$total, array &$details)
    {
        $totalItemTaxes = 0;
        /** @var Tax $tax */
        foreach ($taxes as $tax) {
            $itemTax = $tax->calculate(
                array_merge($parameters, [Tax::PARAM_CUMULATIVE_TAX => $totalItemTaxes])
            );
            $totalItemTaxes += $itemTax;
            $taxCode = $tax->code;
            if (array_key_exists($taxCode, $details)) {
                $details[$taxCode][1] += $itemTax;
            } else {
                $details[$taxCode] = [$tax->attributesToArray(), $itemTax];
            }
        }

        $total += $totalItemTaxes;
    }
}
