<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Facades\Languages;
use \Neomerx\Core\Converters\LanguageConverterGeneric;

final class LanguagesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Languages::INTERFACE_BIND_NAME, App::make(LanguageConverterGeneric::BIND_NAME));
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
        foreach ($this->getApiFacade()->all() as $language) {
            $result[] = $this->getConverter()->convert($language);
        }
        return [$result, null];
    }
}
