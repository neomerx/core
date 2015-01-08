<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Api\Facades\Login;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Response;
use \Neomerx\Core\Api\Login\LoginInterface;
use \Neomerx\Core\Controllers\BaseController;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class LoginControllerJson extends BaseController
{
    final public function login()
    {
        $login    = Input::get('login');
        $password = Input::get('password');

        return $this->tryAndCatchWrapper('loginImpl', [$login, $password]);
    }

    final public function logout()
    {
        return $this->tryAndCatchWrapper('logoutImpl', []);
    }

    protected function loginImpl($login, $password)
    {
        $reply = Login::login($login, $password);
        return [null, $this->convertReplyToHttpCode($reply)];
    }

    protected function logoutImpl()
    {
        $reply = Login::logout();
        return [null, $this->convertReplyToHttpCode($reply)];
    }

    private function convertReplyToHttpCode($reply)
    {
        switch ($reply)
        {
            case LoginInterface::CODE_OK:
                $code = SymfonyResponse::HTTP_OK;
                break;
            case LoginInterface::CODE_ERROR:
                $code = SymfonyResponse::HTTP_UNAUTHORIZED;
                break;
            case LoginInterface::CODE_TOO_MANY_ATTEMPTS:
                $code = SymfonyResponse::HTTP_TOO_MANY_REQUESTS;
                break;
            default:
                $code = SymfonyResponse::HTTP_BAD_REQUEST;
                break;
        }
        return $code;
    }

    /**
     * @param string|array $data
     * @param int          $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatReply($data, $status)
    {
        $response = Response::json($data, $status);
        return $response;
    }
}
