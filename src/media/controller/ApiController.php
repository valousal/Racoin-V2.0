<?php
namespace media\controller ;
use \media\modele\ClientApi ;
use \media\modele\Annonce ;
use \media\modele\Annonceur ;
use \media\modele\Images ;
use \media\modele\Categorie ;
use \media\view\ViewGenerateTokenApi ;
use \media\view\ViewTokenApi ;

include("./src/media/script/miniature.php"); //créer en class avec une methode static



class ApiController extends AbstractController{
	public function __construct(){

	}


	//display form token api
	public function DisplayFormToken(){
		//creer vue
		$v = new ViewGenerateTokenApi();
		$v->display();
	}

	//generate token api
	public function GenerateToken(){		
		if (isset($_POST['mail']) && filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL) ){
			$mail = $_POST['mail'];

			//GENERATE TOKEN
			$token = "";

			// Définir tout les caractères possibles dans le mot de passe, 
			// Il est possible de rajouter des voyelles ou bien des caractères spéciaux
			$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
			$longueur = 30;
			// obtenir le nombre de caractères dans la chaîne précédente
			// cette valeur sera utilisé plus tard
			$longueurMax = strlen($possible);

			if ($longueur > $longueurMax) {
				$longueur = $longueurMax;
			}

			// initialiser le compteur
			$i = 0;

			// ajouter un caractère aléatoire à $mdp jusqu'à ce que $longueur soit atteint
			while ($i < $longueur) {
				// prendre un caractère aléatoire
				$caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);

				// vérifier si le caractère est déjà utilisé dans $mdp
				if (!strstr($token, $caractere)) {
					// Si non, ajouter le caractère à $mdp et augmenter le compteur
					$token .= $caractere;
					$i++;
				}
			}
			
			$clientVerif = ClientApi::where('mail','=', $mail)->first();
			if ($clientVerif != null){
				//REDIRECTION
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('display_login_api'));
			}else{
				$newClientApi = new ClientApi;
				$newClientApi->mail = $mail;
				$newClientApi->token = $token;
				$newClientApi->save();
				$_SESSION['token'] = $token;

				//REDIRECTION
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('display_token_api'));
			}

		}else{
			//REDIRECTION
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('display_login_api'));
		}

	}

	//display token api
	public function DisplayToken(){
		//creer vue
		$v = new ViewTokenApi();
		$v->display();
	}


	/************************************** API REST ******************************************/

	//Accueil API
	public function Api(){
		echo 'lister les differentes fonctionnalités de notre API';

	}

	//annonces
	public function Annonces(){
		$app = \Slim\Slim::getInstance();
		$c = $app->request->get('cat');
		$p = $app->request->get('price');
		$t = $app->request->get('tag');

		if($t=='noTags'){
			$t='noTags';
		}

		if($c!='all'){
			$categorie_search = Categorie::where('nom', '=', $c)->first();
			$idcategorie = $categorie_search->id;
		}else{
			$idcategorie = null;
		}

		if($p=='allPrice'){
			$p='allPrice';
		}


		$annonces = Annonce::where(function($query) use ($p,$t,$c,$idcategorie) {
																	if($t!='noTags'){
			        													$query->where('titre', 'LIKE', "%$t%");
			    													}
																	if($c!='all'){
																        $query->where('id_categorie', '=', "$idcategorie");
																    }
			    													if($p!='allPrice'){
																        $query->where('tarif', '<', "$p");
																    }})
																->get();


		if (!empty($annonces)){
			// Get request object
			$req = $app->request;
			//Get root URI
			$rootUri = $req->getUrl();
			$rootUri .= $req->getRootUri();

			$all = array();
			foreach ($annonces as $key => $annonce){
	            $value['annonce'] = $annonce; //->toJson() pas sur de le mettre .. 
				$value['links'] = array('rel' => "self",
										'href' => "$rootUri/api/annonces/".$annonce->id);
				$all[]=$value;
			}

			$nbAnnonce = $annonces->count();

			//next et prev ????

			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/annonces/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/annonces/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/annonces/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/annonces/?limit=10&offset=$nbAnnonce"),
									);



			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			 /* Invalid */
		     $app->response->headers->set('Content-type','application/json') ;
	         $app->halt(500, json_encode(array("erreur_message"=>'Aucune annonce')));
		}

	
	}

	//Une annonce
	public function Annonce($id){
		$annonce = Annonce::select('id','titre','description','tarif','ville','CP','images','images_ext','id_categorie')->find($id);
		$annonceArray = array();

		$categorie = $annonce->categorie;
		$images = Images::where('id_annonce', '=', $annonce->id)->get();

		
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();


		//$images = Images::where('id_annonce','=', $id)->get();

		$annonceArray['annonce'] = $annonce; //toJson ?????
		$annonce->images = array('href' => "$rootUri/data/img_annonces/".$annonce->images.".".$annonce->images_ext);
		foreach ($images as $key => $value) {
			$annonceArray['photo'][] = array('href' => "$rootUri/data/img_annonces/".$value['name'].".".$value['extension']);
		}
		$annonceArray['links'] = array(		'rel' => 'categorie',
											'id' => $categorie->id,
											'nom' => $categorie->nom,
											'uri' => "$rootUri/api/categorie/".$categorie->nom);
		
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode($annonceArray);

	}

	//Liste categories
	public function Categories(){
		$categories = Categorie::all();


		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$array = array();
		foreach ($categories as $key => $value) {
			$arr['categorie'] = $value; //->toJson() pas sur de le mettre
			$arr['links'] = array('rel' => "self",
									'href' => "$rootUri/api/categorie/".$value->nom);

			$array[] = $arr;
		}


		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode($array);
	}


	//Liste des annonces d'une categorie
	public function AnnoncesByCat($categorie){
		$categorie = Categorie::where('nom', '=', $categorie)->first();
		$idcategorie = $categorie->id;


		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();


		$annonces =  Annonce::select('id','titre','tarif')->where('id_categorie', '=',$categorie->id)->orderBy('id', 'DESC')->get();
		

		$all = array();
		foreach ($annonces as $key => $annonce){
            $value['annonce'] = $annonce; //->toJson() pas sur de le mettre .. 
			$value['links'] = array('rel' => "self",
									'href' => "$rootUri/api/annonces/".$annonce->id);
			$all[]=$value;
		}

		$nbAnnonce = $annonces->count();

		//next et prev ????

		$all['links'] = array(array('rel' => 'prev',
								'href' => "$rootUri/api/annonces/?limit=10&offset=150"),
								array('rel' => 'next',
								'href' => "$rootUri/api/annonces/?limit=10&offset=0"),
								array('rel' => 'first',
								'href' => "$rootUri/api/annonces/?limit=10&offset=150"),
								array('rel' => 'last',
								'href' => "$rootUri/api/annonces/?limit=10&offset=$nbAnnonce"),
								);


		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode($all);
	}

	//ajout d'une annonce
	public function PostAnnonce($app){
		if (isset($_POST['nom'], $_POST['mail'], $_POST['tel'], $_POST["password"],$_POST["titre"], $_POST["description"], $_POST["tarif"], $_POST["ville"], $_POST["CP"],$_POST["categorie"] )
		&& !empty($_POST['nom']) && !empty($_POST['mail']) && !empty($_POST['tel']) && !empty($_POST["password"]) && !empty($_POST["titre"]) && !empty( $_POST["description"]) && !empty( $_POST["tarif"]) && !empty( $_POST["ville"]) && !empty( $_POST["CP"]) && !empty($_POST["categorie"])
		&& filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL) 
		){
			
			$password =  filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
			$password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
			//info categorie
			$categoriePost = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);//$_POST["categorie"];
			$categorie = Categorie::where('nom', '=', $categoriePost)->first();
			$id_categorie = $categorie->id;

			//Info annonce
			$annonce = new Annonce;
			$annonce->password = $password;
			$annonce->titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);//$_POST["titre"];
			$annonce->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);//$_POST["description"];
			$annonce->tarif = filter_input(INPUT_POST, 'tarif', FILTER_SANITIZE_NUMBER_INT);//$_POST["tarif"];
			$annonce->ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);//$_POST["ville"];
			$annonce->CP = filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);//$_POST["CP"];
			$annonce->nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING); //$_POST['nom']; 
			$annonce->mail = $_POST['mail'];
			$annonce->tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);//$_POST['tel'];
			// $annonce->id_annonceurs = $id_annonceur;
			$annonce->id_categorie = $id_categorie;
			$annonce->save();
			$id_annonce = $annonce->id;

			//Gestion image avec la librairy
			$storage = new \Upload\Storage\FileSystem('data/img_annonces'); // /data/img_annonces?
			$file = new \Upload\File('image', $storage);

			// Optionally you can rename the file on upload
			$new_filename = uniqid();
			$file->setName($new_filename);

			// Validate file upload
			// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
			$file->addValidations(array(
			    // Ensure file is of type "image/png"
			   // new \Upload\Validation\Mimetype('image/png'),

			    //You can also add multi mimetype validation
			    new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg')),

			    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
			    new \Upload\Validation\Size('5M')
			));

			// Access data about the file that has been uploaded
			$data = array(
			    'name'       => $file->getNameWithExtension(),
			    'extension'  => $file->getExtension(),
			    'mime'       => $file->getMimetype(),
			    'size'       => $file->getSize(),
			    'md5'        => $file->getMd5(),
			    'dimensions' => $file->getDimensions()
			);	

			// Try to upload file
			try {
			    // Success!
			    $file->upload();
			    $image = new Images;
				$image->name = $new_filename;
				$image->extension = $data['extension'];
				$image->id_annonce = $id_annonce;
				$image->save();

				$annonce->images = $new_filename;
				$annonce->images_ext = $data['extension'];
				$annonce->save();

				//création de la miniature
				$path = "data/img_annonces/".$data['name'];
				$nomMiniature = $new_filename."_miniature"; //nom de la minaiture
				$minia_path =  "data/img_annonces_miniatures/{$nomMiniature}.{$data['extension']}"; //path de la miniature
				$x = 125; //width img
				$y = 75;  //height img

				if (miniaturisation($path, $minia_path, $x, $y)){ //function pour miniaturisation, src/script
					$imageMini = new Images;
					$imageMini->name = $nomMiniature;
					$imageMini->extension = $data['extension'];
					$imageMini->id_annonce = $id_annonce;
					$imageMini->save();
					//echo "Transfert réussi";
					//$app->response->setStatus(201) ;
					//echo "ok?0";
					//$app->response->redirect($app->urlFor('api/annonces', array('id' => $id_annonce)));
					$req = $app->request;
					//Get root URI
					$rootUri = $req->getUrl();
					$rootUri .= $req->getRootUri();
					$annonceArray['annonce'] = $annonce; //toJson ?????
					$imagesAll = Images::where('id_annonce', '=', $annonce->id)->get();
					$annonce->images = array('href' => "$rootUri/data/img_annonces/".$annonce->images.".".$annonce->images_ext);
					foreach ($imagesAll as $key => $value) {
						$annonceArray['photo'][] = array('href' => "$rootUri/data/img_annonces/".$value['name'].".".$value['extension']);
					}
					$annonceArray['links'] = array(		'rel' => 'categorie',
														'id' => $categorie->id,
														'nom' => $categorie->nom,
														'uri' => "$rootUri/api/categorie/".$categorie->nom);
					
					$app->response->setStatus(201) ;
					$app->response->headers->set('Content-type','application/json') ;
					echo json_encode($annonceArray);

				}
			} catch (\Exception $e) {
			    /* Invalid */
		        $app->response->headers->set('Content-type','application/json') ;
	           	$app->halt(500, json_encode(array("erreur_message"=>'images error')));
			}

		}else{
			
			 /* Invalid */
		        $app->response->headers->set('Content-type','application/json') ;
	           	$app->halt(500, json_encode(array("erreur_message"=>'champs invalides')));
		}
	}
	
	//modifier une annonce API
	public function PutAnnonce($app, $id){
		/*if (isset($_POST["titre"], $_POST["description"], $_POST["tarif"], $_POST["ville"], $_POST["CP"],$_POST["categorie"] )
		 && !empty($_POST["titre"]) && !empty( $_POST["description"]) && !empty( $_POST["tarif"]) && !empty( $_POST["ville"]) && !empty( $_POST["CP"]) && !empty($_POST["categorie"])
		){*/
			//password
			$changeAnnonce = Annonce::find($id);
			$hash = $changeAnnonce->password;
			$annonceur_password_clear = filter_var($app->request->put('password'), FILTER_SANITIZE_STRING);//$_POST['password'];
			
			if (password_verify($annonceur_password_clear, $hash)) {

				if ($app->request->put('categorie') != null){
					$categoriePost = filter_var($app->request->put('categorie'), FILTER_SANITIZE_STRING);//filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);//$_POST["categorie"];
					$categorie = Categorie::where('nom', '=', $categoriePost)->first();
					$id_categorie = $categorie->id;
				}else{
					$id_categorie = $changeAnnonce->id_categorie;
				}
				
				

				//gestion informations de l'annonce
				// $changeAnnonce = Annonce::find($id);
				if($app->request->put('titre') != null){
					$titre = filter_var($app->request->put('titre'), FILTER_SANITIZE_STRING);
				}else{
					$titre = $changeAnnonce->titre;
				}
				if($app->request->put('description') != null){
					$description = filter_var($app->request->put('description'), FILTER_SANITIZE_STRING);
				}else{
					$description = $changeAnnonce->description;
				}
				if($app->request->put('tarif') != null){
					$tarif = filter_var($app->request->put('tarif'), FILTER_SANITIZE_STRING);
				}else{
					$tarif = $changeAnnonce->tarif;
				}
				if($app->request->put('ville') != null){
					$ville = filter_var($app->request->put('ville'), FILTER_SANITIZE_STRING);
				}else{
					$ville = $changeAnnonce->ville;
				}
				if($app->request->put('CP') != null){
					$CP = filter_var($app->request->put('CP'), FILTER_SANITIZE_STRING);
				}else{
					$CP = $changeAnnonce->CP;
				}
				$changeAnnonce->titre = $titre;//filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);//$_POST["titre"];
				$changeAnnonce->description = $description;//filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);//$_POST["description"];
				$changeAnnonce->tarif = $tarif;//filter_input(INPUT_POST, 'tarif', FILTER_SANITIZE_STRING);//$_POST["tarif"];
				$changeAnnonce->ville = $ville;//filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);//$_POST["ville"];
				$changeAnnonce->CP = $CP;//filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);//$_POST["CP"];
				$changeAnnonce->id_categorie = $id_categorie;
				$changeAnnonce->save();
				$id_annonce = $changeAnnonce->id;

				/* Valid */
				$req = $app->request;
				//Get root URI
				$rootUri = $req->getUrl();
				$rootUri .= $req->getRootUri();
				$changeAnnonce->images = array('href' => "$rootUri/data/img_annonces/".$changeAnnonce->images.".".$changeAnnonce->images_ext);
				$annonceArray['annonce'] = $changeAnnonce; //toJson ?????
				$changeAnnonce->password = null;
				$annonceArray['links'] = array(		'rel' => 'categorie',
													'id' => $id_categorie,
													'nom' => $categorie->nom,
													'uri' => "$rootUri/api/categorie/".$categorie->nom);
				
				$app->response->setStatus(200) ; //204 pour PUT mais ne retourne aucune données aussi
				$app->response->headers->set('Content-type','application/json') ;
				echo json_encode($annonceArray);
		
		    } else {
		        /* Invalid */
		        $app->response->headers->set('Content-type','application/json') ;
	           	$app->halt(500, json_encode(array("erreur_message"=>'invalide Password')));
		    }
		/*}else{
			/* Invalid */
	        //$app->response->headers->set('Content-type','application/json') ;
	       	//$app->halt(500, json_encode(array("erreur_message"=>'Champs invalides')));
		//}*/
	}



/*END CLASS */
}