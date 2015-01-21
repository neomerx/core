<?php namespace Neomerx\Core\Filesystem;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImageFormat;

interface ImagesInterface
{
    /**
     * Resize image and store result on disk.
     *
     * @param Image       $image
     * @param ImageFormat $format
     * @param bool        $overwrite
     * @param string      $background
     * @param string      $anchor
     * @param bool        $relative
     *
     * @return string
     */
    public function resize(
        Image $image,
        ImageFormat $format,
        $overwrite = true,
        $background = 'rgba(255, 255, 255, 0)',
        $anchor = 'center',
        $relative = false
    );

    /**
     * @param string $fileName
     *
     * @return void
     */
    public function delete($fileName);
}
