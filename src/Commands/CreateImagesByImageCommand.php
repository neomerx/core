<?php namespace Neomerx\Core\Commands;

use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Illuminate\Contracts\Bus\SelfHandling;
use \Neomerx\Core\Filesystem\ImagesInterface;
use \Neomerx\Core\Repositories\Images\ImageRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImagePathRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImageFormatRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class CreateImagesByImageCommand extends Command implements SelfHandling
{
    /**
     * Constructor parameter.
     */
    const PARAM_ID_IMAGE = 'imageId';

    /**
     * @var int
     */
    private $imageId;

    /**
     * @param int $imageId
     */
    public function __construct($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * Execute the command.
     *
     * @param ImageRepositoryInterface       $imageRepo
     * @param ImageFormatRepositoryInterface $formatRepo
     * @param ImagePathRepositoryInterface   $pathRepo
     * @param ImagesInterface                $images
     *
     * @return void
     */
    public function handle(
        ImageRepositoryInterface $imageRepo,
        ImageFormatRepositoryInterface $formatRepo,
        ImagePathRepositoryInterface $pathRepo,
        ImagesInterface $images
    ) {
        $image = $imageRepo->read($this->imageId);
        foreach ($formatRepo->index() as $format) {
            /** @var ImageFormat $format */
            $savedToFileName = $images->resize($image, $format);
            $pathRepo->createWithObjects($image, $format, [ImagePath::FIELD_PATH => $savedToFileName]);
        }
    }
}
