<?php
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfViewMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\SearcherMiddleware;
use App\Middleware\UserMiddleware;


// Routes ayant l'obligation de connection CHERCHEUR + CSRF
$app->group('', function () {
    $this->get("/dictionary", "DictionaryController:index")->setName("dictionary");
	$this->get("/dictionary/export", "DictionaryController:viewExport")->setName("dictionary.export");
	$this->get("/dictionary/export/{format}", "DictionaryController:export")->setName("dictionary.format");
    $this->get("/dictionary/export/{format}/{id}", "DictionaryController:exportSequence")->setName("dictionary.exportSequence");
	$this->get("/dictionary/new", "DictionaryController:new")->setName("dictionary.new");
    $this->get("/dictionary/nuage", "DictionaryController:nuage")->setName("dictionary.nuage");
    $this->get("/dictionary/{id}", "DictionaryController:getById")->setName("dictionary.details");
    $this->get("/dictionary/{element}/id", "DictionaryController:getId")->setName("dictionary.id");
	$this->post("/dictionary", "DictionaryController:create")->setName("dictionary.create");
	$this->put("/dictionary/{id}", "DictionaryController:update")->setName("dictionary.edit");
	//$this->delete("/dictionary/{id}", "DictionaryController:delete")->setName("dictionary.delete");
})->add(new \App\Middleware\CsrfViewMiddleware($container))
    ->add($container->csrf)
    ->add(new SearcherMiddleware($container));

$app->group('', function(){

	$this->post("/postvideo","VideoController:createSequence")->setName("PostVideo");
    $this->get("/videos[/]","VideoController:getVideos")->setName('videos');
	$this->get("/video/{idVideo}[/]","VideoController:index")->setName('ViewVideo');
	$this->get("/getVideosSearcher","VideoController:getVideosSearcher");
	$this->get("/getAllVideos","VideoController:getAllVideos");
	$this->get("/getSearcher/{recherche}","VideoController:getSearcher");
	$this->get("/VideosSearcher/{recherche}","VideoController:getVideoOfSearcher");
	$this->get("/upload","VideoController:upload")->setName('upload');
	$this->get("/objet/{recherche}","VideoController:getObjet");
	$this->get("/method/{recherche}","VideoController:getMethod");
	$this->get("/pseudoCode/{recherche}","SequenceController:pseudoCode");
	$this->get("/methodParam/{recherche}","VideoController:param");
	$this->get("/inMethod/{recherche}","VideoController:inMethod");
	$this->get("/inObjet/{recherche}","VideoController:inObjet");
	$this->get("/allSequence","SequenceController:takeAllSequence");
	$this->post("/image","VideoController:addMiniature");
})->add(new SearcherMiddleware($container));

$app->get("[/]", "HomeController:index")->setName('home');
$app->get("/home[/]", "HomeController:index")->setName('home');
$app->get('/home/mentions[/]', "HomeController:mentions")->setName('home.mentions');
$app->get('/home/technos[/]', "HomeController:technos")->setName('home.technos');



// Routes ayant l'obligation de connexion USER
$app->group('', function () {
    $this->get("/commentaires[/]","HomeController:commentaires")->setName('commentaires');
    $this->get("/random","SequenceController:random");
    $this->post("/commenter[/]","SequenceController:commentaire");
})->add(new UserMiddleware($container));


//Authentification
$app->group('', function () {
    $this->get("/auth/signup[/]", "AuthController:getSignUp")->setName("auth.signup");
    $this->post("/auth/signup[/]", "AuthController:postSignUp");
    $this->get("/auth/signin[/]", "AuthController:getSignIn")->setName("auth.signin");
    $this->post("/auth/signin[/]", "AuthController:postSignIn");
})->add(new CsrfViewMiddleware($container))
    ->add($container->csrf)
    ->add(new GuestMiddleware($container));


// Routes ayant l'obligation de connexion (user + chercheurs)
$app->group('', function () {
    $this->get("/auth/signout[/]", "AuthController:getSignOut")->setName("auth.signout");
    $this->get("/profil[/]", "ProfilController:index")->setName("profil");
    $this->get("/profil/updatePass[/]", "ProfilController:updatePass")->setName("updatePass");
    $this->post("/profil/checkPass[/]", "ProfilController:checkPass")->setName("checkPass");
    $this->get("/profil/updateMail[/]", "ProfilController:updateMail")->setName("updateMail");
    $this->post("/profil/checkMail[/]", "ProfilController:checkMail")->setName("checkMail");
    $this->get("/profil/updateProfilPicture[/]", "ProfilController:updateProfilPicture")->setName("updateProfilPicture");
    $this->post("/profil/checkProfilPicture[/]", "ProfilController:checkProfilPicture")->setName("checkProfilPicture");
    $this->post("/profil/checkProfilPictureUpload[/]", "ProfilController:checkProfilPictureUpload")->setName("checkProfilPictureUpload");
    $this->get("/sequence/{idVideo}/{idSequence}","SequenceController:index")->setName('sequence');
    $this->get("/sequences[/]","SequenceController:sequences")->setName('sequences');
})->add(new AuthMiddleware($container));