<?php

namespace App\Middleware;

class UserMiddleware extends Middleware
{
    public function __invoke($request,  $response, $next){
        if ($this->container->auth->checkLevel() != 'user') {
            return $response->withStatus(301)->withRedirect($this->container->router->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }


}