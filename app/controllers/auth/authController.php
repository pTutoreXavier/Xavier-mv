<?php

namespace App\Controllers\Auth;

use \App\Controllers\Controller;
use \App\Models\User;
use http\Params;
use \Respect\Validation\Validator as v;

class AuthController extends Controller{


    public function getSignOut($request, $response)
    {
        $this->auth->logout();
        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignIn($request, $response)
    {
        return $this->view->render($response, "auth/signin.twig");
    }

    public function postSignIn($request, $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('mail'),
            $request->getParam('mdp')
        );

        if (!$auth) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignUp($request, $response)
    {
		return $this->view->render($response, "auth/signup.twig");
	}

	public function postSignUp($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'nom' => v::notEmpty()->alpha(),
            'prenom' => v::noWhitespace()->notEmpty()->alpha(),
            'naissance' => v::date(),
            'mail' => v::noWhitespace()->notEmpty()->email()->mailAvailable(),
            'mdp' => v::noWhitespace()->notEmpty(),
            'mdpConf' => v::equals($_POST['mdp']),
        ]);


        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }
        $user = User::create([
            'nom' => $request->getParam('nom'),
            'prenom' => $request->getParam('prenom'),
            'mail' => $request->getParam('mail'),
            'mdp' => password_hash($request->getParam('mdp'),PASSWORD_DEFAULT),
            'level' => 500,
            'dateNaissance' => $request->getParam('naissance'),
        ]);
        $this->auth->attempt($user->mail, $request->getParam('mdp'));

        return $response->withRedirect($this->router->pathFor('home'));
	}
}