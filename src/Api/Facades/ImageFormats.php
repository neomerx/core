<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\ImageFormat;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Images\ImageFormatsInterface;

/**
 * @see ImageFormatsInterface
 *
 * @method static ImageFormat create(array $input)
 * @method static ImageFormat read(string $code)
 * @method static void        update(string $code, array $input)
 * @method static void        delete(string $code)
 * @method static Collection  all()
 */
class ImageFormats extends Facade
{
    const INTERFACE_BIND_NAME = ImageFormatsInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
