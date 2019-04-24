<?php
namespace App\Controllers;
use \Slim\Views\Twig as View;
use \App\Models\Video as Video;
use \App\Models\Sequence as Sequence;
use \App\Models\Dictionnaire as Dictionnaire;
use \App\Models\User as User;

class VideoController extends Controller{
	public function index($request, $response){
		$idVideo = $request->getAttribute('route')->getArgument('idVideo');
		$_SESSION["idUser"] = 1;
		$video = Video::find($idVideo);

		$sequences = Sequence::where('idVideo', '=', $idVideo)->get();

		return $this->view->render($response, "video/video.twig", array("video" => $video, 'idVideo' => $idVideo, 'lesSequences' => $sequences));
	}

	public function createSequence($request, $response){

		var_dump($_POST);

		$seq = new Sequence();
		$seq->idVideo = $_POST['idVideo'];
		$seq->debut = $_POST['start'];
		$seq->fin = $_POST["finish"];
		$seq->idUser = $_SESSION['user'];
		if($_POST["parametre"] != null){
			$seq->pseudocode = $_POST["objet"].";".$_POST["method"].";".$_POST["parametre"];
		}
		else{
			$seq->pseudocode = $_POST["objet"].";".$_POST["method"];
		}	
		$seq->miniature = $_POST["miniature"].".png";
		echo $seq;
		$seq->save();

		header('Location: video/'.$_POST['idVideo']);
		exit();
	}

	public function getObjet($request, $response){
		$array = array();

		$objet = Dictionnaire::where("type", "=", "objet")->where("libelle", "like", $request->getAttribute('route')->getArgument('recherche')."%")->get();

		for ($i=0; $i < count($objet); $i++) { 	
			array_push($array, $objet[$i]["libelle"]);
		}

		return json_encode($array);
	}

	public function getMethod($request, $response){
		$array = array();

		$objet = Dictionnaire::where("type", "=", "method")->where("libelle", "like", $request->getAttribute('route')->getArgument('recherche')."%")->get();

		for ($i=0; $i < count($objet); $i++) { 	
			array_push($array, $objet[$i]["libelle"]);
		}

		return json_encode($array);
	}

	public function upload($request, $response){
		return $this->view->render($response, "video/upload.twig");
	}

	public function getVideos($request, $response){
		return $this->view->render($response, "video/lesVideos.twig");
	}

	public function getVideosSearcher($request, $response){
		$array = array();

		$video = Video::where("idChercheur","=", $_SESSION["user"])->get();

		for ($i=0; $i < count($video); $i++) { 	
			array_push($array, $video[$i]);
		}

		return json_encode($array);
	}

	public function getAllVideos($request, $response){
		$array = array();

		$video = Video::get();

		for ($i=0; $i < count($video); $i++) { 	
			array_push($array, $video[$i]);
		}

		return json_encode($array);
	}

	public function getSearcher($request, $response){
		$array = array();
		$recherche = $request->getAttribute('route')->getArgument('recherche');
		$maj = strtoupper($recherche);

		$searcher = User::where("nom","like", $recherche."%")->orWhere("nom","like", $maj."%")->where("level","=","779")->get();

		for ($i=0; $i < count($searcher); $i++) { 	
			array_push($array, $searcher[$i]["nom"]);
		}

		return json_encode($array);
	}

	public function getVideoOfSearcher($request, $response){
		$array = array();
		$recherche = $request->getAttribute('route')->getArgument('recherche');

		$user = User::where("nom","=",$recherche)->get();

		if(count($user) != 0){
			$video = Video::where("idChercheur", "=", $user[0]->id)->get();

			if($video != null){
				for ($i=0; $i < count($video); $i++) { 	
					array_push($array, $video[$i]);
				}
			}
			else{
				return json_encode(0);	
			}
		}
		else{
			return json_encode(0);	
		}
		return json_encode($array);		
	}

	public function param($request, $response){
		$recherche = $request->getAttribute('route')->getArgument('recherche');

		$param = Dictionnaire::select("parametre")->where("type","=","method")->where("libelle","=",$recherche)->first();

		if($param != null){
			if($param->parametre == "objet"){
				$result = 2;
			}
			else{
				$result = 1;
			}
		}
		else{
			$result = 0;
		}	

		return json_encode($result);
	}

	public function inMethod($request, $response){

		$recherche = $request->getAttribute('route')->getArgument('recherche');

		$method = Dictionnaire::where("type","=","method")->where("libelle","=",$recherche)->first();

		if($method != null){
			$result = 1;
		}
		else{
			$result = 0;
		}
		return json_encode($result);

	}

	public function inObjet($request, $response){

		$recherche = $request->getAttribute('route')->getArgument('recherche');

		$objet = Dictionnaire::where("type","=","objet")->where("libelle","=",$recherche)->first();

		if($objet != null){
			$result = 1;
		}
		else{
			$result = 0;
		}
		return json_encode($result);

	}

	public function addMiniature($request, $response){
		$time = time();
		$imageData = $_POST['image'];
		$imageData = str_replace('data:image/png;base64,', '', $imageData);
		$imageData = str_replace(' ', '+', $imageData);
		//$filteredData = substr($imageData, strpos($imageData, ",") + 1);

		$decoded = "";
		for ($i=0; $i < ceil(strlen($imageData)/256); $i++){
		   $decoded = $decoded . base64_decode(substr($imageData,$i*256,256));
		}

		
		$fp = fopen("images/miniatures/". $time .'.png', 'wb');
		fwrite($fp, $decoded);
		fclose($fp);

		/*$upload_dir = "../public/image/miniature/";
		$img = $_POST['image'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_encode($img);
		$file = $upload_dir . time() . ".png";
		//	 $success = file_put_contents($file, $data);*/

		return json_encode($time);

		/*$data = $_POST['image'];
		$image = explode('base64,',$data);
		$fic=fopen("image.png","wb");
		fwrite($fic,base64_decode($image[1]));
		fclose($fic);*/
	}
}