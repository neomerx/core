<?php namespace Neomerx\Core\Filesystem;

class FilesystemAdapter extends \Illuminate\Filesystem\FilesystemAdapter implements FilesystemInterface
{
    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource)
    {
        return $this->driver->writeStream($path, $resource);
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        return $this->driver->readStream($path);
    }
}
