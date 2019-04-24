<?php
namespace App\Controllers;
use \Slim\Views\Twig as View;
use \App\Models\User;
use \App\Models\Sequence as Sequence;
use \App\Models\commentaire as commentaire;
class HomeController extends Controller{

	public function index($request, $response){
		$sequences = Sequence::orderBy('id', 'DESC')->limit(3)->get();
        return $this->view->render($response, "home/home.twig", array(
        	"lastSequence" => $sequences
        ));
	}
	public function commentaires($request, $response){
		$commentaires = Commentaire::where('idUser', '=', $_SESSION['user'])->get();
		$seqsAndComms = Sequence::join('commentaire', 'commentaire.idSequence', '=', 'sequence.id')->where('commentaire.idUser', '=', $_SESSION['user'])->get();
		return $this->view->render($response, "home/commentaires.twig", array(
        	"seqsAndComms" => $seqsAndComms,
        ));
	}

    public function mentions($request, $response){
        return $this->view->render($response, "mentions.twig");
    }

    public function technos($request, $response){
        return $this->view->render($response, "technos.twig");
    }
}