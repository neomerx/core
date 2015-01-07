<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Suppliers;
use \Neomerx\Core\Converters\SupplierConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

final class SuppliersControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Suppliers::INTERFACE_BIND_NAME, App::make(SupplierConverterGeneric::BIND_NAME));
    }

    /**
     * Search suppliers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input, $this->getLanguageFilter($input)]);
    }

    /**
     * @param array  $parameters
     * @param string $languageFilter
     *
     * @return array
     */
    protected function searchImpl(array $parameters, $languageFilter)
    {
        /** @var SupplierConverterGeneric $converter */
        $converter = $this->getConverter();
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($this->getApiFacade()->search($parameters) as $resource) {
            $result[] = $converter->convert($resource);
        }

        return [$result, null];
    }
}
