<?php namespace Neomerx\Core\Filesystem;

use \File;
use \Neomerx\Core\Config;
use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ImageFormat;
use \Intervention\Image\Image as InterventionImage;
use \Neomerx\Core\Exceptions\AccessDeniedFileException;
use \Intervention\Image\Facades\Image as InterventionImageFacade;

class Images implements ImagesInterface
{
    /**
     * @inheritdoc
     */
    public function resize(
        Image $image,
        ImageFormat $format,
        $overwrite = true,
        $background = 'rgba(255, 255, 255, 0)',
        $anchor = 'center',
        $relative = false
    ) {
        $imageFolder = $this->getImageFolder();
        $pathFrom    = $imageFolder.$image->{Image::FIELD_ORIGINAL_FILE};
        $fileTo      = $this->getFormattedFileName($image, $format);
        $pathTo      = $imageFolder.$fileTo;

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var InterventionImage $image */
        $image = InterventionImageFacade::make($pathFrom);

        $width  = $format->{ImageFormat::FIELD_WIDTH};
        $height = $format->{ImageFormat::FIELD_HEIGHT};
        $image = $image->resize($width, $height)->resizeCanvas($width, $height, $anchor, $relative, $background);

        if ($overwrite === true && File::exists($pathTo) === true) {
            $this->delete($pathTo);
        }
        $image->save($pathTo);

        return $fileTo;
    }

    /**
     * @inheritdoc
     */
    public function delete($fileName)
    {
        $imageFolder = $this->getImageFolder();
        File::delete($imageFolder.$fileName) === true ?: S\throwEx(new AccessDeniedFileException($fileName));
    }

    /**
     * @return string
     */
    protected function getImageFolder()
    {
        $path = trim(Config::get(Config::KEY_IMAGE_FOLDER));

        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    /**
     * @param Image       $image
     * @param ImageFormat $format
     *
     * @return string
     */
    protected function getFormattedFileName(Image $image, ImageFormat $format)
    {
        $originalFileName = $image->{Image::FIELD_ORIGINAL_FILE};
        $fileName         = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fileExt          = pathinfo($originalFileName, PATHINFO_EXTENSION);

        return $fileName.'-'.$format->{ImageFormat::FIELD_CODE}.'.'.$fileExt;
    }
}
