<?php
//session start
session_start();


require "vendor/autoload.php";
use \media\controller ;
use \media\modele\DataBaseConnect ;
use \media\modele\Annonce ;
use \media\modele\ClientApi ;

$app = new \Slim\Slim(); //Slim init

DataBaseConnect::setConfig("config/config.ini"); //dataBase connection, $file, mettre le nom de son fichier config!


/*******************************************/
/*************ANNONCES*********************/
/*******************************************/

//Visualiser une annonce 
$app->get( '/annonce/:n', function($n) use ($app){ 
	$url = $app->urlFor('change_annonce', array('n' => $n));
	$url2 = $app->urlFor('delete_annonce', array('n' => $n));

	$annonceController = new controller\AnnonceController;
	$annonceController->displayAnnonce($n,$url,$url2,$app);
})->name('annonce');

/*//Récupérer une image d'une annonce
$app->get( '/image/:n', function($n) use ($app){ 
	$annonceController = new controller\AnnonceController;
	$annonceController->displayImage();
})->name('image');*/




//Visualiser la liste des annonces
$app->get( '/annonces', function() use ($app){ 
	$url = $app->urlFor('annonce', array('n' => '')); //recuperer id de l'annonce ?! car c'est surement pas la meilleure facon de faire

	$annonceController = new controller\AnnonceController;
	$annonceController->displayAllAnnonce($url);
})->name('annonces');



//Page ajout annonce
$app->get( '/add', function() use ($app){ 
	$annonceAdd = new controller\AnnonceController;
	$annonceAdd->displayAddAnnonce();
})->name('add');

//Traitement ajout annonce dans la BDD
$app->post( '/add', function(){ 
	$annonceAdd = new controller\AnnonceController;
	$annonceAdd->addAnnonce();
})->name('add_post');

//Preview ajout annonce dans la BDD
$app->get( '/add/preview', function(){ 
	$annonceAdd = new controller\AnnonceController;
	$annonceAdd->addPreviewAnnonce();
})->name('add_preview');

//Valider après préview ajout annonce dans la BDD
$app->post( '/add/preview/add', function(){ 
	$annonceAdd = new controller\AnnonceController;
	$annonceAdd->addPreviewAddAnnonce();
})->name('add_preview_add');



//Accueil (Visualiser la liste d'annonces aussi) faudrait faire passer la meme chose que la route précédente, mais comment ? 
$app->get( '/', function() use ($app){ 
	$url = $app->urlFor('annonce', array('n' => ''));
	$url2 = $app->urlFor('annonceByCategorie', array('categorie' => ''));


	$annonceController = new controller\AnnonceController;
	$annonceController->displayIndex($url,$url2);
	//echo "index";
})->name('accueil');


//Lister les annonces d'une categorie 
$app->get( '/annonces/:categorie', function($cat) use ($app){ 
	$url = $app->urlFor('annonce', array('n' => ''));

	$annonceController = new controller\AnnonceController;
	$annonceController->displayAnnonceByCat($cat, $url);
})->name('annonceByCategorie');



//Afficher les annonces d'une adresse mail en particulier 
$app->get( '/mesannonces/', function() use ($app){ 
	$formulaire_post = $app->urlFor('formulaire_post');

	$controller = new controller\AnnonceController;
	$controller->displayFormMyAnnonce($formulaire_post);
})->name('formulaire');

$app->post( '/mesannonces/', function() use ($app){ 
	$url = $app->urlFor('annonce', array('n' => ''));

	$controller = new controller\AnnonceController;
	$controller->displayMyAnnonce($url);
})->name('formulaire_post');


//Modifier une annonce
$app->post( '/annonce/modifier/:n', function($n) use($app){ 
	$url = $app->urlFor('valid_change_annonce', array('n' => $n));
	
	$annonceController = new controller\AnnonceController;
	$annonceController->displayFormChangeAnnonce($n,$url,$app);
})->name('change_annonce');


//Modifier une annonce
$app->put( '/annonce/:n', function($n) use($app){ 
	// $url = $app->urlFor('annonce', array('n' => $n));

	$annonceController = new controller\AnnonceController;
	$annonceController->ChangeAnnonce($n,$app);
})->name('valid_change_annonce');

//
//Supprimer une annonce
$app->delete( '/annonce/:n', function($n) use ($app){ 
	// $url = $app->urlFor('annonce', array('n' => $n));

	$annonceController = new controller\AnnonceController;
	$annonceController->DeleteAnnonce($n,$app);
})->name('delete_annonce');





/*******************************************/
/*************ANNONCEUR*********************/
/*******************************************/

//Creer compte pro
$app->get('/registration_pro', function(){
	$proController = new controller\AnnonceurController;
	$proController->displayFormAddPro();
})->name('display_registration_pro');

//Creer compte pro
$app->post('/registration_pro_confirm', function(){
	$proController = new controller\AnnonceurController;
	$proController->AddPro();
})->name('AddPro');

//View profil compte pro
$app->get('/profil/:id', function($id){
	$proController = new controller\AnnonceurController;
	$proController->ViewProfilPro($id);
})->name('view_profil_pro');

//Authentification
$app->post('/pro/connect', function(){
	$proController = new controller\AnnonceurController;
	$proController->AuthentificationPro();
})->name('AuthentificationPro');


//Logout
$app->get('/pro/logout', function(){
	$proController = new controller\AnnonceurController;
	$proController->LogoutPro();
})->name('LogoutPro');

/***************************************************/
/*********************RECHERCHE*********************/
/***************************************************/

//Afficher resultat recherche
$app->get( '/search(/)(:t/)(:p/)(:c/)', function($t=null,$p=null,$c=null) use ($app){ 
	$url = $app->urlFor('annonce', array('n' => ''));
	$url2 = $app->urlFor('annonceByCategorie', array('categorie' => ''));
	$annonceSearch = new controller\AnnonceController;
	$annonceSearch->displaySearch($url,$url2,$t,$p,$c);
})->name('search');

//Traitement de la recherche
$app->post( '/search', function(){ 
	$annonceSearch = new controller\AnnonceController;
	$annonceSearch->search();
})->name('search_post');

/***************************************************/
/*********************  API    *********************/
/***************************************************/

$checkToken = function (){ //vérifie présence et validité du token, à utiliser en middleware pour slim

    return function()
    {
        $app = \Slim\Slim::getInstance();
        if(!isset($_GET['apiKey']) || empty($_GET['apiKey'])){
            $app->response->headers->set('Content-type','application/json') ;
            $app->halt(500, json_encode(array("erreur_message"=>'APIKey is missing')));
            }else{
                $reqApiKey = $_GET['apiKey'];
                $valid_token = ClientApi::where('token','=', $reqApiKey)->get();
                if (count($valid_token) != 1){
                     $app->response->headers->set('Content-type','application/json') ;
           			 $app->halt(500, json_encode(array("erreur_message"=>'invalide APIKey')));
                }
            }
    };

};


//Token API envoit mail
$app->get('/api/login', function(){
	$apiController = new controller\ApiController;
	$apiController->DisplayFormToken();
})->name('display_login_api');


//Token API generate
$app->post('/api/token', function(){
	$apiController = new controller\ApiController;
	$apiController->GenerateToken();
})->name('generate_token_api');


//Token API affiche token
$app->get('/api/token', function(){
	$apiController = new controller\ApiController;
	$apiController->DisplayToken();
})->name('display_token_api');




//Liste les fonctionnalités de l'API
$app->get('/api', function(){
	$apiController = new controller\ApiController;
	$apiController->Api();
})->name('api');


//Liste les annonces API
$app->get('/api/annonces', $checkToken(), function(){
	$apiController = new controller\ApiController;
	$apiController->Annonces();
})->name('annoncesApi');

//Liste des categories API
$app->get('/api/categories', $checkToken(), function(){
	$apiController = new controller\ApiController;
	$apiController->Categories();
})->name('categorieApi');

//Affiche une annonce API
$app->get('/api/annonces/:id', $checkToken(), function($id){
	$apiController = new controller\ApiController;
	$apiController->Annonce($id);
})->name('annonceApi');

//Liste des annonces d'une categorie API
$app->get('/api/categorie/:categorie/annonces', $checkToken(), function($categorie){
	$apiController = new controller\ApiController;
	$apiController->AnnoncesByCat($categorie);
})->name('annoncesCatApi');

//Post une annonce
$app->post('/api/annonces', $checkToken(), function() use($app){
	$apiController = new controller\ApiController;
	$apiController->PostAnnonce($app);
})->name('postAnnonceApi');


/*$app->group('/api', function () use ($app, $checkToken) {
	$c = new controller\ApiController;

	//Liste les annonces API
	$app->get('/api/annonces', $checkToken(), function() use ($app, $c){
		$apiController->Annonces();
	})->name('annoncesApi');

	//Liste des categories API
	$app->get('/api/categories', $checkToken(), function() use ($app, $c){
		$apiController->Categories();
	})->name('categorieApi');

	//Affiche une annonce API
	$app->get('/api/annonces/:id', $checkToken(), function($id) use ($app, $c){
		$apiController->Annonce($id);
	})->name('annonceApi');

	//Liste des annonces d'une categorie API
	$app->get('/api/annoncess/:categorie', $checkToken(), function($categorie) use ($app, $c){
		$apiController->AnnoncesByCat($categorie);
	})->name('annoncesCatApi');
});*/

$app->run();

