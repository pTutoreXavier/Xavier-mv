<?php

namespace App\Middleware;

class SearcherMiddleware extends Middleware
{

    public function __invoke($request,  $response, $next){
        if ($this->container->auth->checkLevel() != 'searcher') {
            return $response->withStatus(301)->withRedirect($this->container->router->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }


}