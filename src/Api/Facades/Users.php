<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\User;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Users\UsersInterface;
use \Illuminate\Database\Eloquent\Collection;

/**
 * @see UsersInterface
 *
 * @method static User       create(array $input)
 * @method static User       read(int $userId)
 * @method static void       update(int $userId, array $input)
 * @method static void       delete(int $userId)
 * @method static Collection search(array $parameters = [])
 */
class Users extends Facade
{
    const INTERFACE_BIND_NAME = UsersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
