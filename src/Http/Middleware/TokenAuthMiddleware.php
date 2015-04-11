<?php namespace Neomerx\Core\Http\Middleware;

use \Closure;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\Core\Auth\Token\User;
use \Neomerx\Core\Auth\Token\TokenManagerInterface;
use \Illuminate\Contracts\Auth\Guard as GuardInterface;

class TokenAuthMiddleware
{
    const HEADER_KEY = 'authToken';

    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var GuardInterface
     */
    private $guard;

    /**
     * @param TokenManagerInterface $tokenManager
     * @param GuardInterface        $guard
     */
    public function __construct(TokenManagerInterface $tokenManager, GuardInterface $guard)
    {
        $this->guard        = $guard;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $loggedIn = $this->loginUsingToken($request);
        return $loggedIn === true ? $next($request) : $this->getInvalidBasicCredentialsResponse();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function loginUsingToken(Request $request)
    {
        $token = $request->header(self::HEADER_KEY);
        return isset($token) === true &&
        ($payload = $this->tokenManager->getPayload($token)) !== null &&
        $this->loginUsingPayload($payload, $token) === true;
    }

    /**
     * Get response for invalid basic authentication credentials.
     *
     * @return Response
     */
    protected function getInvalidBasicCredentialsResponse()
    {
        return new Response(null, 401, ['WWW-Authenticate' => 'Basic']);
    }

    /**
     * @param string $payload
     * @param string $token
     *
     * @return bool
     */
    protected function loginUsingPayload($payload, $token)
    {
        $user = User::jsonDecode($payload, $token);
        $this->guard->login($user);
        return true;
    }
}
