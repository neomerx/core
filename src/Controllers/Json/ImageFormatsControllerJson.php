<?php namespace Neomerx\Core\Controllers\Json;

use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Api\Facades\ImageFormats;
use \Neomerx\Core\Converters\ImageFormatConverterGeneric;

final class ImageFormatsControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(ImageFormats::INTERFACE_BIND_NAME, App::make(ImageFormatConverterGeneric::BIND_NAME));
    }

    /**
     * Get all image formats in the system.
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
        foreach ($this->getApiFacade()->all() as $imageFormat) {
            $result[] = $this->getConverter()->convert($imageFormat);
        }

        return [$result, null];
    }
}
