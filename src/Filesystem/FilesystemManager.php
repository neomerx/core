<?php namespace Neomerx\Core\Filesystem;

use \League\Flysystem\FilesystemInterface  as LeagueFilesystemInterface;

/**
 * @package Neomerx\Core
 */
class FilesystemManager extends \Illuminate\Filesystem\FilesystemManager
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(app());
    }

    /**
     * @inheritdoc
     */
    protected function adapt(LeagueFilesystemInterface $filesystem)
    {
        return new FilesystemAdapter($filesystem);
    }
}
