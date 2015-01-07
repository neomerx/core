<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Models\Image as Model;
use \Neomerx\Core\Api\Images\ImageInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\ImageProperties as PropertiesModel;

class ImageConverterGeneric implements ConverterInterface
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var string
     */
    private $languageFilter;

    /**
     * @var string
     */
    private $formatFiler;

    /**
     * @param string $languageFilter
     */
    public function __construct($languageFilter = null)
    {
        $this->languageFilter = $languageFilter;
    }

    /**
     * @param string $languageFilter
     */
    public function setLanguageFilter($languageFilter)
    {
        $this->languageFilter = $languageFilter;
    }

    /**
     * @return string
     */
    public function getLanguageFilter()
    {
        return $this->languageFilter;
    }

    /**
     * @return string
     */
    public function getFormatFiler()
    {
        return $this->formatFiler;
    }

    /**
     * @param string $formatFiler
     */
    public function setFormatFiler($formatFiler)
    {
        $this->formatFiler = $formatFiler;
    }

    /**
     * Format model to array representation.
     *
     * @param Model $resource
     *
     * @return array
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $result = $resource->attributesToArray();

        $paths = [];
        foreach ($resource->paths as $path) {
            /** @var ImagePath $path */
            $format = $path->format;
            if (!isset($this->formatFiler) or strcasecmp($this->formatFiler, $format->name) === 0) {
                $formatInfo = $format->attributesToArray();
                unset($formatInfo[ImageFormat::FIELD_NAME]);
                $formatInfo[ImagePath::FIELD_PATH] = $path->path;
                $paths[$format->name] = $formatInfo;
            }
        }

        $result[Api::PARAM_PATHS]      = $paths;
        $result[Api::PARAM_PROPERTIES] = $this->regroupLanguageProperties(
            $resource->properties,
            PropertiesModel::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
