<?php namespace Neomerx\Core\Filesystem;

use \Neomerx\Core\Config;
use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Filesystem\Facades\Storage;
use \Intervention\Image\Image as InterventionImage;
use \Neomerx\Core\Exceptions\AccessDeniedFileException;
use \Intervention\Image\Facades\Image as InterventionImageFacade;

/**
 * @package Neomerx\Core
 */
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
        $disk     = $this->getImageDisk();
        $pathFrom = $this->getPathToOriginalsFolder($image);
        list($fileTo, $extension) = $this->getFormattedFileName($image, $format);
        $pathTo = $this->getPathToFormatsFolder($fileTo);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var InterventionImage $image */
        $image = InterventionImageFacade::make($pathFrom);

        $width  = $format->{ImageFormat::FIELD_WIDTH};
        $height = $format->{ImageFormat::FIELD_HEIGHT};
        $image = $image->resize($width, $height)->resizeCanvas($width, $height, $anchor, $relative, $background);

        if ($overwrite === true && $disk->exists($pathTo) === true) {
            $this->checkDiskResult($disk->delete($pathTo), $pathTo);
        }
        $disk->put($pathTo, $image->encode($extension)->getEncoded());

        return $fileTo;
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $disk = $this->getImageDisk();
        $path = $this->getPathToOriginalsFolder($path);
        $this->checkDiskResult($disk->delete($path), $path);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function getPathToOriginalsFolder($fileName)
    {
        return Config::KEY_IMAGE_FOLDER_ORIGINALS . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function getPathToFormatsFolder($fileName)
    {
        return Config::KEY_IMAGE_FOLDER_FORMATS.DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * @return FilesystemInterface
     */
    protected function getImageDisk()
    {
        return Storage::disk(Config::get(Config::KEY_IMAGE_DISK));
    }

    /**
     * @param Image $image
     *
     * @return string
     */
    protected function getFileName(Image $image)
    {
        $fileName = $image->getKey() . '.' . $image->{Image::FIELD_ORIGINAL_EXT};

        return $fileName;
    }

    /**
     * @param Image       $image
     * @param ImageFormat $format
     *
     * @return array
     */
    protected function getFormattedFileName(Image $image, ImageFormat $format)
    {
        $fileName = $image->getKey();
        $fileExt  = $image->{Image::FIELD_ORIGINAL_EXT};

        return [$fileName.'-'.$format->{ImageFormat::FIELD_CODE}.'.'.$fileExt, $fileExt];
    }

    /**
     * @param bool   $result
     * @param string $fileName
     *
     * @throws AccessDeniedFileException
     */
    protected function checkDiskResult($result, $fileName)
    {
        assert('is_bool($result)');
        $result === true ?: S\throwEx(new AccessDeniedFileException($fileName));
    }
}
