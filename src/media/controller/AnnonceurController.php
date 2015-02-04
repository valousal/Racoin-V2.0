<?php
namespace media\controller ;
use \media\modele\Annonce ;
use \media\modele\Annonceur ;
use \media\modele\Images ;
use \media\modele\Categorie ;
use \media\modele\ClientApi ;
use \media\view\ViewFormAddPro ;
use \media\view\ViewAnnonceurProfil ;

class AnnonceurController extends AbstractController{
	public function __construct(){

	}	

	//display formulaire inscription compte pro annonceur
	public function displayFormAddPro(){
		if(!isset($_SESSION['id'])){
			$categories = Categorie::all();

			//creer vue
			$v = new ViewFormAddPro($categories);
			$v->display();
		}else{
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('accueil'));
		}
	}

	//confirm inscription compte pro annonceur
	public function AddPro(){
		if (isset($_POST['nom'], $_POST['mail'], $_POST['tel'], $_POST["password"],$_POST["ville"], $_POST["CP"],$_POST["categorie"] )
			&& !empty($_POST['nom']) && !empty($_POST['mail']) && !empty($_POST['tel']) && !empty($_POST["password"]) && !empty( $_POST["ville"]) && !empty( $_POST["CP"]) && !empty($_POST["categorie"])
			&& filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL) 
			){


			//VERIFIER SI ANNONCEUR EXISTE DEJA
			$mailAnnonceur = $_POST['mail'];
			$mailAnnonceur = Annonceur::where('mail', '=', $mailAnnonceur)->first();
			
			if($mailAnnonceur == null){ //Je pensais que c'est empy ou isset
				$annonceur = new Annonceur;
				$annonceur->nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING); //$_POST['nom']; 
				$annonceur->mail = $_POST['mail'];
				$annonceur->tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);//$_POST['tel'];
				$annonceur->ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);//$_POST['ville'];
				$annonceur->CP = filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);//$_POST['CP'];
				
				//HASH
				$password =  filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);//$_POST["password"];
				$hash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
				$annonceur->password = $hash;

				//traitement récupérer id de la categorie favorite
				$categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);//$_POST['categorie'];
				$categorie = Categorie::where('nom', '=', $categorie)->first();
				$annonceur->id_cat_fav = $categorie->id;
				$annonceur->save(); 
				$id_annonceur = $annonceur->id;

				//creer vue
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('view_profil_pro', array('id' => $id_annonceur)));
			}else{
				//rediriger vers le formulaire d'inscription reprenant les informations saisie
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('display_registration_pro'));
			}
		}else{
			//rediriger vers le formulaire d'inscription reprenant les informations saisie
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('display_registration_pro'));
		}
	}


	//display profil annonceur Pro
	public function ViewProfilPro($id){
		if(isset($_SESSION['id'])){
			$annonceur = Annonceur::find($id);
			$categorie = Categorie::where('id', '=', $annonceur->id_cat_fav)->first();
			
			//annonces de l'annonceur
			$annonces = Annonce::where('mail','=' ,$annonceur->mail)->get();

			//Token du mec si mail correspond
			$TokenApi = ClientApi::where('mail','=', $annonceur->mail)->first();

			//creer vue
			if (!empty($annonceur)){
				$v = new ViewAnnonceurProfil($annonceur, $categorie, $annonces,$TokenApi);
				$v->display();
			}else{
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('accueil'), 303);
			}
		}else{
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('accueil'), 303);
		}
	}


	//authentification annonceur Pro
	public function AuthentificationPro(){
		if(isset($_POST['mail']) && isset($_POST['password']) && filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL) && !empty($_POST['mail']) && !empty($_POST['password']) ){
		    $mailAnnonceur = $_POST['mail'];
		    $password =  filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);//$_POST["password"];
			
			$annonceur = Annonceur::where('mail','=', $mailAnnonceur)->first(); 
			if($annonceur != null){
				$hash = $annonceur->password;
				if(password_verify($password, $hash)){
					/* Valid */
					//session_start();
					$_SESSION['id'] = $annonceur->id;
					$_SESSION['name'] = $annonceur->nom;
					$_SESSION['mail'] = $annonceur->mail;
					$_SESSION['tel'] = $annonceur->tel;
					$_SESSION['password'] = $annonceur->password;
					$app = \Slim\Slim::getInstance();
					$app->response->redirect($app->urlFor('accueil'), 303);
				}else{
					/* Invalid */
					$app = \Slim\Slim::getInstance();
					$app->response->redirect($app->urlFor('accueil'), 303);
				}
			}else{
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('accueil'), 303);
			}
			
		}else{
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('accueil'), 303);
		}
	}

	//logout annonceur Pro
	public function LogoutPro(){
		// session_start(); //doit redéclarer la session ...?!
		session_destroy();
		$app = \Slim\Slim::getInstance();
		$app->response->redirect($app->urlFor('accueil'), 303);
	}
	
}