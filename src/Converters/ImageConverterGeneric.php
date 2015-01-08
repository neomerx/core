<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Models\ImageProperties;
use \Neomerx\Core\Api\Images\ImageInterface as Api;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class ImageConverterGeneric extends BasicConverterWithLanguageFilter
{
    use LanguagePropertiesTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var string
     */
    private $formatFiler;

    /**
     * @param string $languageFilter
     * @param string $formatFilter
     */
    public function __construct($languageFilter = null, $formatFilter = null)
    {
        parent::__construct($languageFilter);
        $this->formatFiler = $formatFilter;
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
     * @param Image $image
     *
     * @return array
     */
    public function convert($image = null)
    {
        if ($image === null) {
            return null;
        }

        ($image instanceof Image) ?: S\throwEx(new InvalidArgumentException('image'));

        $result = $image->attributesToArray();

        $paths = [];
        foreach ($image->paths as $path) {
            /** @var \Neomerx\Core\Models\ImagePath $path */
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
            $image->properties,
            ImageProperties::FIELD_LANGUAGE,
            $this->getLanguageFilter()
        );

        return $result;
    }
}
