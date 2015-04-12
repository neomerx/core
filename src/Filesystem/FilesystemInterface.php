<?php namespace Neomerx\Core\Filesystem;

use \Illuminate\Contracts\Filesystem\Cloud;
use \Illuminate\Contracts\Filesystem\Filesystem;

/**
 * @package Neomerx\Core
 */
interface FilesystemInterface extends Filesystem, Cloud
{
    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function writeStream($path, $resource);

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return false|resource
     */
    public function readStream($path);
}
