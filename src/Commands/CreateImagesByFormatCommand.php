<?php namespace Neomerx\Core\Commands;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Illuminate\Contracts\Bus\SelfHandling;
use \Neomerx\Core\Filesystem\ImagesInterface;
use \Neomerx\Core\Repositories\Images\ImageRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImagePathRepositoryInterface;
use \Neomerx\Core\Repositories\Images\ImageFormatRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class CreateImagesByFormatCommand extends Command implements SelfHandling
{
    /**
     * Constructor parameter.
     */
    const PARAM_FORMAT_CODE = 'formatCode';

    /**
     * @var string
     */
    private $formatCode;

    /**
     * @param string $formatCode
     */
    public function __construct($formatCode)
    {
        $this->formatCode = $formatCode;
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
        $format = $formatRepo->read($this->formatCode);
        foreach ($imageRepo->search() as $image) {
            /** @var Image $image */
            $savedToFileName = $images->resize($image, $format);
            $pathRepo->instance($image, $format, [ImagePath::FIELD_PATH => $savedToFileName])->saveOrFail();
        }
    }
}
