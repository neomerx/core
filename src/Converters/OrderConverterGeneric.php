<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Order;
use \Neomerx\Core\Models\Variant;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Orders\OrdersInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class OrderConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    const PARAM_STATUS_CODE      = Api::PARAM_ORDER_STATUS_CODE;
    const PARAM_DETAILS          = Api::PARAM_ORDER_DETAILS;
    const PARAM_BILLING_ADDRESS  = Api::PARAM_ADDRESSES_BILLING;
    const PARAM_SHIPPING_ADDRESS = Api::PARAM_ADDRESSES_SHIPPING;
    const PARAM_STORE_CODE       = Api::PARAM_STORE_CODE;

    /**
     * @var ConverterInterface
     */
    private $addressConverter;

    /**
     * @param ConverterInterface $addressConverter
     */
    public function __construct(ConverterInterface $addressConverter = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->addressConverter = $addressConverter ? $addressConverter : App::make(AddressConverterGeneric::BIND_NAME);
    }

    /**
     * Format model to array representation.
     *
     * @param Order $order
     *
     * @return array
     */
    public function convert($order = null)
    {
        if ($order === null) {
            return null;
        }

        ($order instanceof Order) ?: S\throwEx(new InvalidArgumentException('order'));

        $result = $order->attributesToArray();
        $result[Api::PARAM_ORDER_STATUS_CODE]  = $order->status->code;
        $result[Api::PARAM_ADDRESSES_BILLING]  = $this->addressConverter->convert($order->billing_address);
        $result[Api::PARAM_ADDRESSES_SHIPPING] = $this->addressConverter->convert($order->shipping_address);
        $result[Api::PARAM_STORE_CODE]         = $order->store ? $order->store->code : null;

        $details = [];
        foreach ($order->details as $detailsRow) {
            /** @var \Neomerx\Core\Models\OrderDetails $detailsRow */
            $details[] = $detailsRow->attributesToArray();
            $details[Variant::FIELD_SKU] = $detailsRow->variant->sku;
        }
        $result[Api::PARAM_ORDER_DETAILS] = $details;

        return $result;
    }
}
