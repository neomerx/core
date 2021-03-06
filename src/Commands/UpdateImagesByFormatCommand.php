<?php namespace Neomerx\Core\Commands;

use \Neomerx\Core\Models\Image;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Illuminate\Contracts\Bus\SelfHandling;
use \Neomerx\Core\Filesystem\ImagesInterface;
use \Neomerx\Core\Repositories\Images\ImageFormatRepositoryInterface;

/**
 * @package Neomerx\Core
 */
class UpdateImagesByFormatCommand extends Command implements SelfHandling
{
    /**
     * Constructor parameter.
     */
    const PARAM_FORMAT_ID = 'formatId';

    /**
     * @var int
     */
    private $formatId;

    /**
     * @param string $formatId
     */
    public function __construct($formatId)
    {
        $this->formatId = $formatId;
    }

    /**
     * Execute the command.
     *
     * @param ImageFormatRepositoryInterface $formatRepo
     * @param ImagesInterface                $images
     *
     * @return void
     */
    public function handle(ImageFormatRepositoryInterface $formatRepo, ImagesInterface $images)
    {
        $format = $formatRepo->read($this->formatId);
        foreach ($format->{ImageFormat::FIELD_PATHS} as $path) {
            /** @var ImagePath $path */
            /** @var Image $image */
            $image = $path->{ImagePath::FIELD_IMAGE};
            $path->{ImagePath::FIELD_PATH} = $images->resize($image, $format);
            $path->isDirty() === false ?: $path->saveOrFail();
        }
    }
}
