<?php namespace Neomerx\Core\Api\Facades;

use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Login\LoginInterface;

/**
 * @see LoginInterface
 *
 * @method static int login(string $login, string $password)
 * @method static int logout()
 */
class Login extends Facade
{
    const INTERFACE_BIND_NAME = LoginInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
