<?php namespace Neomerx\Core\Filesystem\Facades;

use \Closure;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Filesystem\FilesystemInterface;
use \Neomerx\Core\Filesystem\FilesystemManager;

/**
 * @method static FilesystemInterface drive(string $name = null)
 * @method static FilesystemInterface disk(string $name = null)
 * @method static FilesystemInterface createLocalDriver(array $config)
 * @method static FilesystemInterface createS3Driver(array $config)
 * @method static FilesystemInterface createRackspaceDriver(array $config)
 * @method static string getDefaultDriver()
 * @method static Storage extend(string $driver, Closure $callback)
 */
class Storage extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return FilesystemManager::class;
    }
}
