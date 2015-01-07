<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Facades\Regions;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Converters\RegionConverterGeneric;

final class RegionsControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Regions::INTERFACE_BIND_NAME, App::make(RegionConverterGeneric::BIND_NAME));
    }

    /**
     * Search regions.
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
     * @param array $parameters
     *
     * @return array
     */
    protected function searchImpl(array $parameters)
    {
        $resources = $this->getApiFacade()->search($parameters);

        $result = [];
        foreach ($resources as $resource) {
            $result[] = $this->getConverter()->convert($resource);
        }

        return [$result, null];
    }
}
