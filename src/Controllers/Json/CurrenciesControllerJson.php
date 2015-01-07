<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Currencies;
use \Neomerx\Core\Converters\CurrencyConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

final class CurrenciesControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Currencies::INTERFACE_BIND_NAME, App::make(CurrencyConverterGeneric::BIND_NAME));
    }

    /**
     * Get all currencies in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('getAll', [$this->getLanguageFilter(Input::all())]);
    }

    /**
     * @param string $languageFilter
     *
     * @return array
     */
    protected function getAll($languageFilter)
    {
        $currencies = $this->getApiFacade()->all();

        /** @var CurrencyConverterGeneric $converter */
        $converter = $this->getConverter();
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($currencies as $currency) {
            $result[] = $converter->convert($currency);
        }

        return [$result, null];
    }
}
