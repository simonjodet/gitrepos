<?php
namespace Gitrepos;
use \Symfony\Component\HttpFoundation\Request;

class UserRequestMatcher implements \Symfony\Component\HttpFoundation\RequestMatcherInterface
{
    /**
     * Decides whether the rule(s) implemented by the strategy matches the supplied request.
     *
     * @param Request $request The request to check for a match
     *
     * @return Boolean true if the request matches, false otherwise
     *
     * @api
     */
    public function matches(Request $request)
    {
        $anonymous_routes = array(
            '/login',
            '/register'
        );
        return !in_array($request->getRequestUri(), $anonymous_routes);
    }
}