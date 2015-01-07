<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Api\Facades\Taxes;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Converters\TaxConverterGeneric;

final class TaxesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Taxes::INTERFACE_BIND_NAME, App::make(TaxConverterGeneric::BIND_NAME));
    }

    /**
     * Get all languages in the system.
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
