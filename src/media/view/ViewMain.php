<?php
namespace media\view;

abstract class ViewMain extends View{
	protected $url = null;

	public function __construct(){ 
		$app = new \Slim\Slim();

		// Get request object
		$req = $app->request;

		//Get root URI
		$rootUri = $req->getRootUri();
		//Get resource URI
		//$resourceUri = $req->getResourceUri();

		//Add ROOTURI (les deux méthodes fonctionnent)
		$url = $this->addVar("rootUri", $rootUri);
		//$this->arrayVar['rootUri'] = $rootUri ;

		//Add url for header (bonne méthode?)
		$app = \Slim\Slim::getInstance();
		//index
		$index = $app->urlFor('accueil');
		$index = $this->addVar("accueil", $index);
		//annonces
		$annonces = $app->urlFor('annonces');
		$annonces = $this->addVar("annonces", $annonces);
		//Add
		$add = $app->urlFor('add');
		$add = $this->addVar("add", $add);
		//Mes annonces
		$formulaire = $app->urlFor('formulaire');
		$formulaire = $this->addVar("formulaire", $formulaire);
		//Search
		$search = $app->urlFor('search');
		$search = $this->addVar("search", $search);

		//Token API formulaire
		$formulaireApi = $app->urlFor('display_login_api');
		$formulaireApi = $this->addVar('display_login_api', $formulaireApi);
		//Token API formulaire
		$api = $app->urlFor('api');
		$api = $this->addVar('api', $api);



		//AUTHENTIFICATION PRO ET CREATION COMPTE PRO URL
		$urlRegistrationPro = $app->urlFor('display_registration_pro');
		$urlRegistrationPro = $this->addVar('url_registration', $urlRegistrationPro);

		$urlAuthentificationPro = $app->urlFor('AuthentificationPro');
		$urlAuthentificationPro = $this->addVar('urlAuthentificationPro', $urlAuthentificationPro);

		//session_start();
		if(isset($_SESSION['id'])){
			$authentification = $this->addVar('authentification', $_SESSION);

			//Lien profil Pro
			$urlProfilPro = $app->urlFor('view_profil_pro', array('id' => $_SESSION['id']));
			$urlProfilPro = $this->addVar('view_profil_pro', $urlProfilPro);

			//Logout profil Pro
			$urlLogoutPro = $app->urlFor('LogoutPro');
			$urlLogoutPro = $this->addVar('LogoutPro', $urlLogoutPro);

		}
	}

}