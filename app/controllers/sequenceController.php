<?php
namespace App\Controllers;
use \Slim\Views\Twig as View;
use \App\Models\Video as Video;
use \App\Models\Sequence as Sequence;
use \App\Models\Commentaire as Commentaire;

class SequenceController extends Controller{
	public function index($request, $response){

		$idVideo = $request->getAttribute('route')->getArgument('idVideo');
		$idSequence = $request->getAttribute('route')->getArgument('idSequence');

		$video = Video::find($idVideo);

		$seq = Sequence::where('id', '=', $idSequence)->where('idVideo', '=', $idVideo)->first();

		$message = Commentaire::where('idUser', '=', $_SESSION["user"])->where('idSequence','=',$idSequence)->first();

		$lesCommentaires = Commentaire::where('idSequence',"=",$idSequence)->get();

		return $this->view->render($response, "video/sequence.twig", array("video" => $video, "seq" => $seq, "message" => $message, 'idVideo' => $idVideo, 'idSequence' => $idSequence, 'commentaires' => $lesCommentaires));
	}

	public function commentaire($request, $response){
		if($_POST["message"] != ""){
			$c = new Commentaire();
			$c->idUser = $_SESSION["user"];
			$c->idSequence = $_POST['idSequence'];
			$c->commentaire = $_POST["message"];

			$c->save();
		}
		header('Location: sequence/'.$_POST['idVideo'].'/'.$_POST['idSequence']);
		exit();
	}

	public function sequences($request, $response){
		return $this->view->render($response, "video/lesSequences.twig");
	}

	public function random($request, $response){
		$array = array();
		$arrayRandom = array();
		$rando = 10;
		$possible = true;
		$id = Sequence::get();

		if(count($id) < $rando){
			$rando = count($id);
		}

		for ($i=0; $i < $rando; $i++) { 
			$alea = rand(0,count($id) - 1);

			if(count($arrayRandom) > 0){
				for ($j=0; $j < count($arrayRandom); $j++) { 
					if($arrayRandom[$j] == $alea){
						$possible = false;
					}
				}
				if($possible){
					array_push($array, $id[$alea]);
					array_push($arrayRandom, $alea);
				}
				else{
					$i--;
					$possible = true;
				}
			}
			else{
				array_push($arrayRandom, $alea);
				array_push($array, $id[$alea]);
			}
		}

		return json_encode($array);
	}

	public function pseudoCode($request, $response){
		$array = array();
		$recherche = $request->getAttribute('route')->getArgument('recherche');

		$sequence = Sequence::where("pseudocode","like", "%".$recherche."%")->get();

		for ($i=0; $i < count($sequence); $i++) { 	
			array_push($array, $sequence[$i]);
		}

		return json_encode($array);
	}

	public function takeAllSequence($request, $response){
		$array = array();
		$sequences = Sequence::get();

		for ($i=0; $i < count($sequences); $i++) { 	
			array_push($array, $sequences[$i]);
		}

		return json_encode($array);
	}
}