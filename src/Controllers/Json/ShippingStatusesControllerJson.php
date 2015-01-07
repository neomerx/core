<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Facades\ShippingStatuses;
use \Neomerx\Core\Converters\ShippingStatusConverterGeneric;

final class ShippingStatusesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(
            ShippingStatuses::INTERFACE_BIND_NAME,
            App::make(ShippingStatusConverterGeneric::BIND_NAME)
        );
    }

    /**
     * Get all shipping statuses in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        return $this->tryAndCatchWrapper('readAll', []);
    }

    /**
     * @return array
     */
    protected function readAll()
    {
        $result = [];
        foreach ($this->getApiFacade()->all() as $resource) {
            $result[] = $this->getConverter()->convert($resource);
        }

        return [$result, null];
    }
}
