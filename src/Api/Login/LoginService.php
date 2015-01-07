<?php namespace Neomerx\Core\Api\Login;

use \Neomerx\Core\Models\User;
use \Illuminate\Support\Facades\Auth;

class LoginService implements LoginInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * {@inheritdoc}
     */
    public function login($login, $password)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = Auth::attempt([
            User::FIELD_EMAIL    => $login,
            User::FIELD_PASSWORD => $password,
            User::FIELD_ACTIVE   => 1
        ]);
        return $success ? LoginInterface::CODE_OK : LoginInterface::CODE_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Auth::logout();
        return LoginInterface::CODE_OK;
    }
}
