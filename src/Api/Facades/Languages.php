<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Languages\LanguagesInterface;

/**
 * @see LanguagesInterface
 *
 * @method static Language   create(array $input)
 * @method static Language   read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection all()
 */
class Languages extends Facade
{
    const INTERFACE_BIND_NAME = LanguagesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
