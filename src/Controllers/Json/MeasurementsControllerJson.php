<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Api\Facades\Measurements;
use \Neomerx\Core\Converters\MeasurementConverterGeneric;
use \Neomerx\Core\Controllers\Json\Traits\LanguageFilterTrait;

final class MeasurementsControllerJson extends BaseControllerJson
{
    use LanguageFilterTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Measurements::INTERFACE_BIND_NAME, App::make(MeasurementConverterGeneric::BIND_NAME));
    }

    /**
     * Get all measurements in the system.
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
        /** @var MeasurementConverterGeneric $converter */
        $converter = $this->getConverter();
        $converter->setLanguageFilter($languageFilter);

        $result = [];
        foreach ($this->getApiFacade()->all() as $resource) {
            $result[] = $converter->convert($resource);
        }

        return [$result, null];
    }
}
