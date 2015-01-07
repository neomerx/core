<?php namespace Neomerx\Core\Api\Images;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\ImageFormat;

class ImageFormatArgs extends EventArgs
{
    /**
     * @var ImageFormat
     */
    private $imageFormat;

    /**
     * @param string      $name
     * @param ImageFormat $imageFormat
     * @param EventArgs   $args
     */
    public function __construct($name, ImageFormat $imageFormat, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->imageFormat = $imageFormat;
    }

    /**
     * @return ImageFormat
     */
    public function getModel()
    {
        return $this->imageFormat;
    }
}
