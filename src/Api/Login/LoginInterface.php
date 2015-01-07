<?php namespace Neomerx\Core\Api\Login;

interface LoginInterface
{
    const CODE_OK                = 0;
    const CODE_ERROR             = 1;
    const CODE_TOO_MANY_ATTEMPTS = 2;

    /**
     * @param string $login
     * @param string $password
     *
     * @return int
     */
    public function login($login, $password);

    /**
     * @return int
     */
    public function logout();
}
