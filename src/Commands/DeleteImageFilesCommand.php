<?php namespace Neomerx\Core\Commands;

use \Illuminate\Contracts\Bus\SelfHandling;
use \Neomerx\Core\Filesystem\ImagesInterface;

class DeleteImageFilesCommand extends Command implements SelfHandling
{
    /**
     * Constructor parameter.
     */
    const PARAM_FILE_NAMES = 'fileNames';

    /**
     * @var array<string>
     */
    private $fileNames;

    /**
     * @param array<string> $fileNames
     */
    public function __construct(array $fileNames)
    {
        $this->fileNames = $fileNames;
    }

    /**
     * Execute the command.
     *
     * @param ImagesInterface $images
     *
     * @return void
     */
    public function handle(ImagesInterface $images)
    {
        foreach ($this->fileNames as $fileName) {
            /** @var string $fileName */
            $images->delete($fileName);
        }
    }
}
