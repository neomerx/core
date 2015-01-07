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
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function parseInputShipping(array $input)
    {
        if (!isset($input[Orders::PARAM_SHIPPING][Orders::PARAM_SHIPPING_TYPE])) {
            return [null, null];
        }

        switch ($input[Orders::PARAM_SHIPPING][Orders::PARAM_SHIPPING_TYPE]) {
            case Orders::PARAM_SHIPPING_TYPE_DELIVERY:
                $isDelivery = true;
                break;
            case Orders::PARAM_SHIPPING_TYPE_PICKUP:
                $isDelivery = false;
                break;
            default:
                throw new InvalidArgumentException(Orders::PARAM_SHIPPING_TYPE);
        }

        $arrayKey = $isDelivery ? Orders::PARAM_SHIPPING_CARRIER_CODE : Orders::PARAM_SHIPPING_PLACE_CODE;
        isset($input[Orders::PARAM_SHIPPING][$arrayKey]) ?: S\throwEx(new InvalidArgumentException($arrayKey));

        if ($isDelivery) {
            // delivery by carrier
            /** @var Carrier $carrierModel */
            /** @noinspection PhpUndefinedMethodInspection */
            $carrierModel = App::make(Carrier::BIND_NAME);
            $carrier = $carrierModel->selectByCode(
                $input[Orders::PARAM_SHIPPING][Orders::PARAM_SHIPPING_CARRIER_CODE]
            )->firstOrFail();
            $store = null;
        } else {
            // pickup from store
            $carrier = null;
            /** @var Store $storeModel */
            /** @noinspection PhpUndefinedMethodInspection */
            $storeModel = App::make(Store::BIND_NAME);
            /** @noinspection PhpUndefinedMethodInspection */
            $store = $storeModel->selectByCode(
                $input[Orders::PARAM_SHIPPING][Orders::PARAM_SHIPPING_PLACE_CODE]
            )->withAddress()->firstOrFail();
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
        switch ($customerData[Orders::PARAM_CUSTOMER_TYPE]) {
            case Orders::PARAM_CUSTOMER_TYPE_NEW:
                $isNew = true;
                break;
            case Orders::PARAM_CUSTOMER_TYPE_EXISTING:
                $isNew = false;
                break;
            default:
                throw new InvalidArgumentException(Orders::PARAM_CUSTOMER_TYPE);
        }

        $customerId = null;
        if ($isNew) {

            unset($customerData[Orders::PARAM_CUSTOMER_TYPE]);
            unset($customerData[Orders::PARAM_ADDRESSES]);

        } else {

            $customerId  = S\array_get_value($customerData, Orders::PARAM_CUSTOMER_ID);
            $customerId !== null ?: S\throwEx(new InvalidArgumentException(Orders::PARAM_CUSTOMER_ID));
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

        isset($addressData[Orders::PARAM_ADDRESS_TYPE]) ?:
            S\throwEx(new InvalidArgumentException(Orders::PARAM_ADDRESS_TYPE));

        switch ($addressData[Orders::PARAM_ADDRESS_TYPE]) {
            case Orders::PARAM_ADDRESS_TYPE_NEW:
                $isNew = true;
                break;
            case Orders::PARAM_ADDRESS_TYPE_EXISTING:
                $isNew = false;
                break;
            default:
                throw new InvalidArgumentException(Orders::PARAM_ADDRESS_TYPE);
        }

        if ($isNew) {
            unset($addressData[Orders::PARAM_ADDRESS_TYPE]);
            $addressId = null;
        } else {
            $addressId  =  S\array_get_value($addressData, Orders::PARAM_ADDRESS_ID);
            $addressId !== null ?: S\throwEx(new InvalidArgumentException(Orders::PARAM_ADDRESS_ID));
            $addressData = null;
        }

        return [$isNew, $addressData, $addressId];
    }
}
