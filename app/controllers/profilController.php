<?php
namespace App\Controllers;
use \Slim\Views\Twig as View;
use \App\Models\User as User;
use http\Params;
use \Respect\Validation\Validator as v;

class ProfilController extends Controller{
	public function index($request, $response){
		$user = User::find($_SESSION['user']);
    	$dateNaissance = \DateTime::createFromFormat('Y-m-d', $user->dateNaissance)->format('d F Y');
    	$dateCreation = \DateTime::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('d F Y'); 
    	$moisNaissanceEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    	$moisNaissanceFr = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];	
    	$dateNaissance = str_replace($moisNaissanceEn, $moisNaissanceFr, $dateNaissance);
    	$dateCreation = str_replace($moisNaissanceEn, $moisNaissanceFr, $dateCreation);
		return $this->view->render($response, 'profil/profil.twig', array(
			"nom" => $user->nom,
			"prenom" => $user->prenom,
			"mail" => $user->mail,
			"dateNaissance" => $dateNaissance,
			"level" => $user->level,
			"avatar" => $user->avatar,
			"created_at" => $dateCreation
		));
	}
	public function updatePass($request, $response){
		$user = User::find($_SESSION['user']);
		if (isset($_SESSION['errorPass'])) {
			echo $_SESSION['errorPass'];
			unset($_SESSION['errorPass']);
		}
		return $this->view->render($response, 'profil/profilUpdatePass.twig', array(
			"mdp" => $user->mdp,
		));
	}
	public function updateMail($request, $response){
		$user = User::find($_SESSION['user']);
		if (isset($_SESSION['errorMail'])) {
			echo $_SESSION['errorMail'];
			unset($_SESSION['errorMail']);
		}
		return $this->view->render($response, 'profil/profilUpdateMail.twig');
	}
	public function checkPass($request, $response){
		if ($_POST['newPass'] != $_POST['newPass2']) {
			$_SESSION['errorPass'] = 'Les mot de passe saisie ne correspondent pas';
			return $response->withRedirect($this->router->pathFor('updatePass'));
		}
		elseif (strlen($_POST['newPass']) < 8){
			$_SESSION['errorPass'] = 'Le mot de passe est trop court (8 caractères)';
			return $response->withRedirect($this->router->pathFor('updatePass'));
		}
		elseif ($_POST['newPass'] == $_POST['currentPass']){
			$_SESSION['errorPass'] = 'Le mot de passe ne doit pas être identique a l\'ancien';
			return $response->withRedirect($this->router->pathFor('updatePass'));
		}
		else{ 
			$passHash = password_hash ($_POST['newPass'] , PASSWORD_DEFAULT);
			$user = User::find($_SESSION['user']);
			if (password_verify ( $_POST['currentPass'] , $user->mdp )) {
				echo "ok";
				$user->mdp = $passHash;
				$user->save();
				return $response->withRedirect($this->router->pathFor('profil'));
			}
			else
			{
				$_SESSION['errorPass'] = 'L\'ancien mot de passe ne correspond pas avec la base de données.';
				return $response->withRedirect($this->router->pathFor('updatePass'));
			}
		}

	}
	public function checkMail($request, $response){
		if ($_POST['mail'] != $_POST['mail2']) {
			$_SESSION['errorMail']='diff';
			return $response->withRedirect($this->router->pathFor('updateMail'));
		}
		elseif(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
			$_SESSION['errorMail']='non valide';
			return $response->withRedirect($this->router->pathFor('updateMail'));
		}else{
			$user = User::find($_SESSION['user']);
			$user->mail = $_POST['mail'];
			$user->save();
			return $response->withRedirect($this->router->pathFor('profil'));
		}
	}
	public function updateProfilPicture($request, $response){	
		if (isset($_SESSION['erreur'])) {
			echo $_SESSION['erreur'];
			unset($_SESSION['erreur']);
		}
		$user = User::find($_SESSION['user']);
		return $this->view->render($response, 'profil/profilUpdateProfilPicture.twig', array(
			"mdp" => $user->mdp,
		));
	}
	public function checkProfilPicture($request, $response){
		$user = User::find($_SESSION['user']);
		$user->avatar = $_POST['profilPicture'].".png";
		$user->save();
		return $response->withRedirect($this->router->pathFor('profil'));
	}
	public function checkProfilPictureUpload($request, $response){
		$user = User::find($_SESSION['user']);
		$extensions_valides = array( 'jpg' , 'jpeg', 'png' );
		$extension_upload = strtolower(  substr(  strrchr($_FILES['picture']['name'], '.')  ,1)  );
		if ($_FILES['picture']['size'] > 2000000) {
			$_SESSION['erreur'] = "<script>alert('Le fichier est trop gros (2Mo max)')</script>";
			return $response->withRedirect($this->router->pathFor('updateProfilPicture'));
		}
		elseif (! in_array($extension_upload,$extensions_valides) ){
			$_SESSION['erreur'] = "<script>alert('L\'extenssion n\'est pas autorisé (jpg, jpeg ou png)')</script>";
			return $response->withRedirect($this->router->pathFor('updateProfilPicture'));
		} 
		else
		{
			$rand = md5(uniqid(rand(), true));
			$nom = "../public/images/avatar/custom/{$rand}.{$extension_upload}";
			$resultat = move_uploaded_file($_FILES['picture']['tmp_name'],$nom);
			if ($resultat) echo "Transfert réussi";
			$user->avatar = "custom/{$rand}.{$extension_upload}";
			$user->save();
			return $response->withRedirect($this->router->pathFor('profil'));
		}

	}
}