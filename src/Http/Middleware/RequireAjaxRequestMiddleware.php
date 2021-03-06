<?php namespace Neomerx\Core\Http\Middleware;

use \Closure;
use \Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;

/**
 * This middleware could be used as a first defense line from CSRF attacks because AJAX header cannot
 * be added to the cross domain request without the consent of the server via CORS.
 *
 * @package Neomerx\Core
 */
class RequireAjaxRequestMiddleware
{
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
        return $request->ajax() === true ? $next($request) : $this->getInvalidAjaxHeadersResponse();
    }

    /**
     * Get response for a request with invalid 'X-Requested-With' header.
     *
     * @return JsonResponse
     */
    protected function getInvalidAjaxHeadersResponse()
    {
        return new JsonResponse('\'X-Requested-With: XMLHttpRequest\' header required', 400);
    }
}
