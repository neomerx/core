<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Facades\ProductTaxTypes;
use \Neomerx\Core\Converters\ProductTaxTypeConverterGeneric;

final class ProductTaxTypesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(ProductTaxTypes::INTERFACE_BIND_NAME, App::make(ProductTaxTypeConverterGeneric::BIND_NAME));
    }

    /**
     * Get all product tax types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
