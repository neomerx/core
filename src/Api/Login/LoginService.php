<?php namespace Neomerx\Core\Api\Login;

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
        $success = Auth::attempt(['email' => $login, 'password' => $password, 'active' => 1]);
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
