<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Store;
use \Neomerx\Core\Api\Cart\Cart;
use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Api\Cart\CartItem;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Products\VariantCache;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

trait ParseInputTrait
{
    /**
     * @param array $input
     *
     * @return array
     */
    private function parseInput(array $input)
    {
        return array_merge(
            $this->parseInputShipping($input),
            $this->parseInputCustomer($input),
            $this->parseInputAddresses($input),
            [$this->parseCart($input)]
        );
    }

    /**
     * @param array $input
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function parseInputShipping(array $input)
    {
        if (!isset($input[Orders::PARAM_SHIPPING][Orders::PARAM_SHIPPING_TYPE])) {
            return [null, null];
        }

        $isDelivery = $this->mapValue2D($input, Orders::PARAM_SHIPPING, Orders::PARAM_SHIPPING_TYPE, [
            Orders::PARAM_SHIPPING_TYPE_DELIVERY => true,
            Orders::PARAM_SHIPPING_TYPE_PICKUP   => false,
        ]);

        if ($isDelivery) {
            // delivery by carrier
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var \Neomerx\Core\Models\Carrier $carrierModel */
            $carrierModel = App::make(Carrier::BIND_NAME);
            $carrierCode = S\array_get_value_2D_ex($input, Orders::PARAM_SHIPPING, Orders::PARAM_SHIPPING_CARRIER_CODE);
            $carrier     = $carrierModel->selectByCode($carrierCode)->firstOrFail();
            $store       = null;
        } else {
            // pickup from store
            $carrier = null;
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var \Neomerx\Core\Models\Store $storeModel */
            $storeModel = App::make(Store::BIND_NAME);
            $storeCode  = S\array_get_value_2D_ex($input, Orders::PARAM_SHIPPING, Orders::PARAM_SHIPPING_PLACE_CODE);
            $store = $storeModel->selectByCode($storeCode)->withAddress()->firstOrFail();
        }

        return [$carrier, $store];
    }

    /**
     * @param array $input
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function parseInputCustomer(array $input)
    {
        if (!isset($input[Orders::PARAM_CUSTOMER][Orders::PARAM_CUSTOMER_TYPE])) {
            return [null, null, null];
        }

        $customerData = $input[Orders::PARAM_CUSTOMER];
        $isNew = $this->mapValue1D($customerData, Orders::PARAM_CUSTOMER_TYPE, [
            Orders::PARAM_CUSTOMER_TYPE_NEW      => true,
            Orders::PARAM_CUSTOMER_TYPE_EXISTING => false,
        ]);

        if ($isNew) {
            $customerId = null;
            unset($customerData[Orders::PARAM_CUSTOMER_TYPE]);
            unset($customerData[Orders::PARAM_ADDRESSES]);
        } else {
            $customerId   = S\array_get_value_ex($customerData, Orders::PARAM_CUSTOMER_ID);
            $customerData = null;
        }

        // TODO separate parsing customer data for customer's call and admin call
        // If we are here from customer API call then customerId should be ignored (shouldn't present actually)
        // we have to take either current customer or create new one.
        // If this API call originates from admin API then we have to use of create new customer.
        // For now we just load customer by Id.
        $customer = null;
        if ($customerId !== null) {
            /** @noinspection PhpUndefinedMethodInspection */
            $customer = App::make(Customer::BIND_NAME)->withTypeRiskAndLanguage()->findOrFail($customerId);
        }

        return [$isNew, $customerData, $customer];
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function parseInputAddresses(array $input)
    {
        if (!isset($input[Orders::PARAM_CUSTOMER][Orders::PARAM_ADDRESSES])) {
            return [null, null, null, null, null, null];
        }

        return array_merge(
            $this->parseInputAddress(S\array_get_value(
                $input[Orders::PARAM_CUSTOMER][Orders::PARAM_ADDRESSES],
                Orders::PARAM_ADDRESSES_BILLING
            )),
            $this->parseInputAddress(S\array_get_value(
                $input[Orders::PARAM_CUSTOMER][Orders::PARAM_ADDRESSES],
                Orders::PARAM_ADDRESSES_SHIPPING
            ))
        );
    }

    /**
     * @param array $input
     *
     * @return Cart
     *
     * @throws InvalidArgumentException
     */
    private function parseCart(array $input)
    {
        $detailsData = S\array_get_value($input, Orders::PARAM_ORDER_DETAILS);

        // read SKUs and quantities. If SKU is more than once on the list then merge
        // we want all SKUs to read all the associated products at once on the next step
        $skuAndQty = [];
        foreach ($detailsData as $detailsRow) {
            $sku      = S\array_get_value($detailsRow, Orders::PARAM_ORDER_DETAILS_SKU);
            $quantity = S\array_get_value($detailsRow, Orders::PARAM_ORDER_DETAILS_QUANTITY);

            settype($quantity, 'float');

            if (isset($skuAndQty[$sku])) {
                $skuAndQty[$sku] += $quantity;
            } else {
                $skuAndQty[$sku] = $quantity;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var VariantCache $variantCache */
        $variantCache = App::make(VariantCache::BIND_NAME);

        $cartSKUs = array_keys($skuAndQty);
        $variantCache->cacheMany($cartSKUs);

        $cart = new Cart();
        foreach ($cartSKUs as $sku) {
            $variant = $variantCache->getObject($sku);

            // check if SKU found
            $variant !== null ?: S\throwEx(new InvalidArgumentException(Orders::PARAM_ORDER_DETAILS_SKU));

            $cart->push(new CartItem($variant, $skuAndQty[$sku]));
        }

        return $cart;
    }

    /**
     * @param array $addressData
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function parseInputAddress(array $addressData = null)
    {
        if ($addressData === null) {
            return [null, null, null];
        }

        /** @var bool $isNew */
        $isNew = $this->mapValue1D($addressData, Orders::PARAM_ADDRESS_TYPE, [
            Orders::PARAM_ADDRESS_TYPE_NEW      => true,
            Orders::PARAM_ADDRESS_TYPE_EXISTING => false
        ]);

        if ($isNew) {
            unset($addressData[Orders::PARAM_ADDRESS_TYPE]);
            $addressId = null;
        } else {
            $addressId   = S\array_get_value_ex($addressData, Orders::PARAM_ADDRESS_ID);
            $addressData = null;
        }

        return [$isNew, $addressData, $addressId];
    }

    private function mapValue1D(array $input, $key, array $values)
    {
        $inputValue = S\array_get_value_1D_ex($input, $key);
        return S\array_get_value_1D_ex($values, $inputValue);
    }

    private function mapValue2D(array $input, $key1, $key2, array $values)
    {
        $inputValue = S\array_get_value_2D_ex($input, $key1, $key2);
        return S\array_get_value_1D_ex($values, $inputValue);
    }
}
