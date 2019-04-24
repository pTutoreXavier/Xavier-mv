<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__."/../vendor/autoload.php";

$app = new \Slim\App([
	"settings" => [
		"displayErrorDetails" => true
	]
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection(parse_ini_file(__DIR__."/conf/conf.ini"));
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container["db"] = function($container) use ($capsule){
	return $capsule;
};

$container['auth'] = function ($container) {
    return new \App\Auth\Auth;
};

$container["view"] = function($container){
	$view = new \Slim\Views\Twig(__DIR__."/../ressources/views", [
		"cache" => false,
	]);
	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->getEnvironment()->addGlobal('auth', [
	    'check' => $container->auth->check(),
        'user' => $container->auth->user(),
        'level' => $container->auth->checkLevel(),
    ]);

	return $view;
};

$container['validator'] = function($container){
    return new \App\validation\Validator;
};


// CONTROLLERS
$container["HomeController"] = function($container){
	return new \App\Controllers\HomeController($container);
};
$container["AuthController"] = function($container){
	return new \App\Controllers\Auth\AuthController($container);
};
$container["DictionaryController"] = function($container){
	return new \App\Controllers\DictionaryController($container);
};
$container["ProfilController"] = function($container){
	return new \App\Controllers\ProfilController($container);
};
$container["SequenceController"] = function($container){
	return new \App\Controllers\SequenceController($container);
};
$container["VideoController"] = function($container){
	return new \App\Controllers\VideoController($container);
};

// SECURITE : CSRF
$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

// MIDDLEWARES
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));

//REGLES DE VALIDATION
v::with('App\\Validation\\Rules\\');

require __DIR__."/../app/routes.php";