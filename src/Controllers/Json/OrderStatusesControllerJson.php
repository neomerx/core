<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Models\OrderStatus;
use \Neomerx\Core\Api\Facades\OrderStatuses;
use \Neomerx\Core\Converters\OrderStatusConverterGeneric;

final class OrderStatusesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(OrderStatuses::INTERFACE_BIND_NAME, App::make(OrderStatusConverterGeneric::BIND_NAME));
    }

    /**
     * Get all order statuses in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        return $this->tryAndCatchWrapper('readAll', []);
    }

    /**
     * Add a new available status for $codeFrom.
     *
     * @param string $codeFrom
     * @param string $codeTo
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeAvailable($codeFrom, $codeTo)
    {
        return $this->tryAndCatchWrapper('storeAvailableImpl', [$codeFrom, $codeTo]);
    }

    /**
     * Remove available status from $codeFrom.
     *
     * @param string $codeFrom
     * @param string $codeTo
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function destroyAvailable($codeFrom, $codeTo)
    {
        return $this->tryAndCatchWrapper('destroyAvailableImpl', [$codeFrom, $codeTo]);
    }

    /**
     * @return array
     */
    protected function readAll()
    {
        $result = [];
        foreach ($this->getApiFacade()->all() as $status) {
            $result[] = $this->getConverter()->convert($status);
        }

        return [$result, null];
    }

    /**
     * @param string $codeFrom
     * @param string $codeTo
     *
     * @return array
     */
    protected function storeAvailableImpl($codeFrom, $codeTo)
    {
        $this->getApiFacade()->addAvailable(
            $this->getModelByCode(OrderStatus::BIND_NAME, $codeFrom),
            $this->getModelByCode(OrderStatus::BIND_NAME, $codeTo)
        );
        return [null, null];
    }

    /**
     * @param string $codeFrom
     * @param string $codeTo
     *
     * @return array
     */
    protected function destroyAvailableImpl($codeFrom, $codeTo)
    {
        $this->getApiFacade()->removeAvailable(
            $this->getModelByCode(OrderStatus::BIND_NAME, $codeFrom),
            $this->getModelByCode(OrderStatus::BIND_NAME, $codeTo)
        );
        return [null, null];
    }
}
