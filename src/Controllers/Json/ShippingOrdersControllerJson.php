<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Models\ShippingOrder;
use \Neomerx\Core\Api\Facades\ShippingOrders;
use \Neomerx\Core\Converters\ShippingOrderConverterGeneric;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ShippingOrdersControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(ShippingOrders::INTERFACE_BIND_NAME, App::make(ShippingOrderConverterGeneric::BIND_NAME));
    }

    /**
     * Search shipping orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input]);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function createResource(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $shippingOrder = $this->getApiFacade()->create($input);
        return [['id' => $shippingOrder->{ShippingOrder::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function searchImpl(array $parameters)
    {
        $result = [];
        foreach ($this->getApiFacade()->search($parameters) as $resource) {
            $result[] = $this->getConverter()->convert($resource);
        }

        return [$result, null];
    }
}
