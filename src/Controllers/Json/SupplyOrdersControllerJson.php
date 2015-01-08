<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\SupplyOrder;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\SupplyOrders;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Neomerx\Core\Converters\SupplyOrderConverterGeneric;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class SupplyOrdersControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(SupplyOrders::INTERFACE_BIND_NAME, App::make(SupplyOrderConverterGeneric::BIND_NAME));
    }

    /**
     * Search manufacturers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input]);
    }

    final public function storeDetails($supplyOrderId)
    {
        settype($supplyOrderId, 'int');

        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('storeDetailsImpl', [$supplyOrderId, $input]);
    }

    /**
     * Update supply order details record.
     *
     * @param int $detailsId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateDetails($detailsId)
    {
        settype($detailsId, 'int');

        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('updateDetailsImpl', [$detailsId, $input]);
    }

    /**
     * Destroy supply order details record.
     *
     * @param int $detailsId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyDetails($detailsId)
    {
        settype($detailsId, 'int');

        return $this->tryAndCatchWrapper('destroyDetailsImpl', [$detailsId]);
    }

    /**
     * @param array $input
     *
     * @return array<mixed,mixed>
     */
    protected function createResource(array $input)
    {
        $supplyOrder = $this->getApiFacade()->create($input);
        return [['id' => $supplyOrder->{SupplyOrder::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
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

    /**
     * @param int   $supplyOrderId
     * @param array $details
     *
     * @return array
     */
    protected function storeDetailsImpl($supplyOrderId, array $details)
    {
        /** @var SupplyOrderDetails $detailsRow */
        $detailsRow = $this->getApiFacade()->createDetails(
            $this->getModelById(SupplyOrder::BIND_NAME, $supplyOrderId),
            $details
        );
        return [$detailsRow->{SupplyOrderDetails::FIELD_ID}, SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param int   $detailsId
     * @param array $details
     *
     * @return array
     */
    protected function updateDetailsImpl($detailsId, array $details)
    {
        $this->getApiFacade()->updateDetails(
            $this->getApiFacade()->readDetails($detailsId),
            $details
        );
        return [null, null];
    }

    /**
     * @param int $detailsId
     *
     * @return array
     */
    protected function destroyDetailsImpl($detailsId)
    {
        $this->getApiFacade()->deleteDetails($detailsId);
        return [null, null];
    }
}
