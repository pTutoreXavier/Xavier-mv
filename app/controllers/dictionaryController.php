<?php
namespace App\Controllers;
use \Slim\Views\Twig as View;
use \Respect\Validation\Validator as v;
use \App\Models\Dictionnaire;
use \App\Models\Sequence;
use \App\Models\Commentaire;

class DictionaryController extends Controller{
	//page d'accueil du dictionnaire
	public function index($request, $response){
		$methods = Dictionnaire::where("type", "method")->get();
		$objects = Dictionnaire::where("type", "objet")->get();
		return $this->view->render($response, "dictionary/dictionary.twig", ["elements" => ["Objets" => $objects, "Fonctions" => $methods]]);
	}

	//récupère un élément et redirige vers l'action (modification, export ou affichage)
	public function getById($request, $response, $args){
		$element = Dictionnaire::find($args["id"]);
		//page de modification
		if($request->getParam("action") == "edit"){
			$response = $this->view->render($response, "dictionary/edit.twig", ["element" => $element, "parametres" => explode("; ", $element->parametre)]);
		}
		//suppression
		/*elseif($request->getParam("action") == "delete"){
			$response = $this->delete($request, $response, $args);
		}*/
		//export
		elseif($request->getParam("action") == "export"){
			$args["format"] = $request->getParam("format");
			$args["sequence"] = $element;
			$response = $this->export($request, $response, $args);
		}
		//details de l'élément
		else{
			$sequences = Sequence::where("pseudocode", "like", $element->libelle.";%")->orWhere("pseudocode", "like", "%;".$element->libelle.";%")->orWhere("pseudocode", "like", "%;".$element->libelle)->get();
			foreach($sequences as $sequence){
				$sequence->pseudocode = explode(";", $sequence->pseudocode);
				$sequence->commentaires = Commentaire::select("commentaire")->where("idSequence", $sequence->id)->get();
			}
			$response = $this->view->render($response, "dictionary/details.twig", ["element" => $element, "sequences" => $sequences]);
		}		
		return $response;
	}

	//page de création d'un élément
	public function new($request, $response){
		if(in_array($request->getParam("type"), ["type", "method"])){
			return $this->view->render($response, "dictionary/new.twig", ["type" => $request->getParam("type")]);
		}
		return $response->withRedirect($this->router->pathFor('dictionary'));
	}

	//création de l'élément
	public function create($request, $response, $args){
		$params = $request->getParams();
	    $validation = $this->validator->validate($request, [
		    'libelle' => v::notEmpty()->alpha()
		]);
		if($params["type"] == "method" && $params["parametre"] != ""){
	    	$validation = $this->validator->validate($request, [
			    'parametre' => v::alpha()
			]);	    	
	    }
        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor('dictionary.new')."?type=".$request->getParam("type"));
        }
		if(Dictionnaire::where("libelle", $params["libelle"])->first() == null){
			$element = new Dictionnaire;
			$element->type = $params["type"];
			$element->libelle = $params["libelle"];
			if($params["type"] == "method" && $params["parametre"] != ""){
				$element->parametre = $params["parametre"];
			}
			$element->save();
		}
		return $response->withRedirect($this->router->pathFor('dictionary'));
	}

	//modification de l'élément
	public function update($request, $response, $args){
		$params = $request->getParams();
		$element = Dictionnaire::find($args["id"]);
		$validation = $this->validator->validate($request, [
		    'libelle' => v::notEmpty()->alpha()
		]);
		if($element->type == "method" && $params["parametre"] != ""){
	    	$validation = $this->validator->validate($request, [
			    'parametre' => v::alpha()
			]);	    	
	    }
        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor('dictionary.edit', ["id" => $args["id"]])."?action=edit");
        }
        $element->libelle = $params["libelle"];
        if($element->type == "method"  && $params["parametre"] != "" ){
        	$element->parametre = $params["parametre"];
        }
		$element->save();
	    return $response->withRedirect($this->router->pathFor("dictionary.details", ["id" => $args["id"]]));
	}

	//suppression de l'élément
	/*public function delete($request, $response, $args){
        $element = Dictionnaire::find($args["id"]);
	    $element->delete();
	    return $response->withRedirect($this->router->pathFor("dictionary"));
	}*/

	//page pour choisir dans quel format exporter les données (tout le dictionnaire)
	public function viewExport($request, $response, $args){
		return $this->view->render($response, "dictionary/export.twig", ["formats" => ["xml", "csv", "json"]]);
	}

	//création des données et du fichier qui sera téléchargé (tout ou un élément)
	public function export($request, $response, $args){
		$format = $args["format"];
		if(in_array($format, ["xml", "csv", "json"])){
			$data = [];
			if(isset($args["sequence"])){
				$sequences = Sequence::where("pseudocode", "like", $args["sequence"]->libelle.";%")->orWhere("pseudocode", "like", "%;".$args["sequence"]->libelle.";%")->orWhere("pseudocode", "like", "%;".$args["sequence"]->libelle)->get();
			}
			else{
				$sequences = Sequence::select(["id", "pseudocode"])->get();
			}
			foreach($sequences as $sequence){
				$pseudocode = explode(";", $sequence->pseudocode);
				$s = "";
				for($i = 0; $i < count($pseudocode); $i++){
					$s .= $pseudocode[$i];
					if($i == 0){
						$s .=  ".";
					}
					if($i == 1){
						$s .= "(";
					}
				}
				$s .= ")";
				$commentaires = Commentaire::where("idSequence", "=", $sequence->id)->get();
				$data[$s] = [];
				foreach ($commentaires as $commentaire){
					array_push($data[$s], $commentaire->commentaire);
				}
			}
			if(isset($args["sequence"])){
				$name = $args["sequence"]->libelle."_".$format."_".date("d-m-Y");
			}
			else{
				$name = "dictionnaire_".$format."_".date("d-m-Y");
			}
			$path = "../public/";
			$this->$format($data, $name, $path);
			// désactive la mise en cache
			header("Cache-Control: no-cache, must-revalidate");
			header("Cache-Control: post-check=0,pre-check=0");
			header("Cache-Control: max-age=0");
			header("Pragma: no-cache");
			header("Expires: 0");			 
			// force le téléchargement du fichier avec le bon nom
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment; filename="'.$name.'.'.$format.'"');
			readfile($path.$name.'.'.$format);
			unlink($path.$name.'.'.$format);
		}
		else{
			return $response->withRedirect($this->router->pathFor('dictionary'));
		}
	}

	//export dans le format choisi d'une séquence (pseudo et commentaires)
	public function exportSequence($request, $response, $args){
		$format = $args["format"];
		if(in_array($format, ["xml", "csv", "json"])){
			$data = [];
			$sequence = Sequence::find($args["id"]);
			$pseudocode = explode(";", $sequence->pseudocode);
			$s = "";
			for($i = 0; $i < count($pseudocode); $i++){
				$s .= $pseudocode[$i];
				if($i == 0){
					$s .=  ".";
				}
				if($i == 1){
					$s .= "(";
				}
			}
			$s .= ")";
			$commentaires = Commentaire::where("idSequence", "=", $sequence->id)->get();
			$data[$s] = [];
			foreach ($commentaires as $commentaire){
				array_push($data[$s], $commentaire->commentaire);
			}
			$name = $s."_".$format."_".date("d-m-Y");
			$path = "../public/";
			$this->$format($data, $name, $path);
			// désactive la mise en cache
			header("Cache-Control: no-cache, must-revalidate");
			header("Cache-Control: post-check=0,pre-check=0");
			header("Cache-Control: max-age=0");
			header("Pragma: no-cache");
			header("Expires: 0");			 
			// force le téléchargement du fichier avec le bon nom
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment; filename="'.$name.'.'.$format.'"');
			readfile($path.$name.'.'.$format);
			unlink($path.$name.'.'.$format);
		}
		else{
			return $response->withRedirect($this->router->pathFor('dictionary'));
		}
	}

	//création d'un fichier xml à partir des données
	private function xml($data, $name, $path){
		$xml = new \XMLWriter();
		$xml->openMemory();
		$xml->startDocument('2.0', 'utf-8');
		$xml->startElement("data");	
		foreach($data as $key => $value){
			$xml->startElement("sequence");			
			$xml->writeAttribute("pseudocode", $key);
			foreach ($value as $commentaire){
				$xml->writeElement('commentaire', $commentaire);
			}
			$xml->endElement();
		}
		$xml->endElement();
		$xml->endDocument();
		$file = fopen($path.$name.".xml", "w+");
		fwrite($file, $xml->flush());
		fclose($file);
	}

	//création d'un fichier csv à partir des données
	private function csv($data, $name, $path){
		$file = fopen($path.$name.".csv", "w+");
		fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($file, ["sequence", "commentaires"], ";");
		foreach($data as $key => $value){
			for($i = 0; $i < count($value); $i++){
				if($i == 0){
					fputcsv($file, [$key, $value[$i]], ";");
				}
				else{
					fputcsv($file, ["", $value[$i]], ";");
				}
			}
		}
		fclose($file);
	}

	//création d'un fichier json à partir des données
	private function json($data, $name, $path){
		$file = fopen($path.$name.".json", "w+");
		fwrite($file, json_encode($data));
		fclose($file);
	}

	//affichage du nuage de mot, récupération du libelle et du nombre
	public function nuage($request, $response, $args){
		$elements = Dictionnaire::select(["libelle", "type"])->get();
		$data = [];
		foreach($elements as $element){
			$count = Sequence::where("pseudocode", "like", $element->libelle.";%")->orWhere("pseudocode", "like", "%;".$element->libelle.";%")->orWhere("pseudocode", "like", "%;".$element->libelle)->count();
			if($element->type == "method"){
				$element->libelle .= "()";
			}
			array_push($data, ["libelle" => $element->libelle, "count" => $count]);
		}
		return $this->view->render($response, "dictionary/nuage.twig", ["elements" => $data]);
	}

	//récupération de l'id par rapport au libellé (click nuage et détails d'un élément)
	public function getId($request, $response, $args){
		$libelle = $args["element"];
		if(substr($libelle, -2, 2) == "()"){
			$libelle = substr($libelle, 0, strlen($libelle) - 2);
		}
		$element = Dictionnaire::where("libelle", $libelle)->first();
		return $response->withRedirect($this->router->pathFor("dictionary.details", ["id" => $element->id]));
	}
}