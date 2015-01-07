<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Api\Facades\Countries;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Converters\RegionConverterGeneric;
use \Neomerx\Core\Converters\CountryConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

final class CountriesControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Countries::INTERFACE_BIND_NAME, App::make(CountryConverterGeneric::BIND_NAME));
    }

    /**
     * Get all countries in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->tryAndCatchWrapper('getAll', [$this->getLanguageFilter(Input::all())]);
    }

    /**
     * Get all regions of the country.
     *
     * @param string $code Country code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function regions($code)
    {
        return $this->tryAndCatchWrapper('getRegions', [$code]);
    }

    /**
     * @param string $languageFilter
     *
     * @return array
     */
    protected function getAll($languageFilter)
    {
        $countries = $this->getApiFacade()->all();

        /** @var CountryConverterGeneric $converter */
        $converter = $this->getConverter();
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($countries as $country) {
            $result[] = $converter->convert($country);
        }

        return [$result, null];
    }

    /**
     * @param string $code
     *
     * @return array
     */
    protected function getRegions($code)
    {
        $regions = $this->getApiFacade()->regions($this->getModelByCode(Country::BIND_NAME, $code));

        $result = [];
        /** @var RegionConverterGeneric $converter */
        /** @noinspection PhpUndefinedMethodInspection */
        $converter = App::make(RegionConverterGeneric::BIND_NAME);
        foreach ($regions as $region) {
            $result[] = $converter->convert($region);
        }

        return [$result, null];
    }
}
