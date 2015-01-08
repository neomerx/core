<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Api\Customers\CustomersInterface as Api;

class CustomerConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Customer $customer
     *
     * @return null|array<mixed,mixed>
     */
    public function convert($customer = null)
    {
        if ($customer === null) {
            return null;
        }

        ($customer instanceof Customer) ?: S\throwEx(new InvalidArgumentException('customer'));

        $customerRisk = $customer->risk;
        $result = array_merge($customer->attributesToArray(), [
            Api::PARAM_TYPE_CODE     => $customer->type->code,
            Api::PARAM_RISK_CODE     => $customerRisk ? $customerRisk->code : null,
            Api::PARAM_LANGUAGE_CODE => $customer->language->iso_code,
        ]);

        return $result;
    }
}
