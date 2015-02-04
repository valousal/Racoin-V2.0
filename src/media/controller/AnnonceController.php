<?php
namespace media\controller ;
use \media\modele\Annonce ;
use \media\modele\Annonceur ;
use \media\modele\Images ;
use \media\modele\Categorie ;
use \media\view\ViewAnnonce ;
use \media\view\ViewAllAnnonce ;
use \media\view\ViewAddAnnonce ;
use \media\view\ViewIndex ;
use \media\view\ViewAnnonceByCat ;
use \media\view\ViewFormMyAnnonce ;
use \media\view\ViewMyAnnonce ;
use \media\view\ViewFormChangeAnnonce ;
use \media\view\ViewPreviewAnnonce ;
use \media\view\ViewSearch ;
use \media\script\CRSFGuard ;
/*use  function \media\script\test\miniaturisation ; //php 5.6+ ... u_u*/
include("./src/media/script/miniature.php"); //créer en class avec une methode static



class AnnonceController extends AbstractController{
	public function __construct(){

	}	


	//Affichage de l'index
	public function displayIndex($url,$cat){
		$annonceLast = Annonce::with('images','categorie')->orderBy('id', 'DESC')->take(12)->get();
		$annonceAll = Annonce::with('images','categorie')->orderBy('id', 'DESC')->get();
		$categorie = Categorie::all();
		//creer vue
		$v = new ViewIndex($annonceAll, $url,$categorie,$annonceLast);
		$v->display();
	}



	//Affichage d'une annonnce en particulier
	public function displayAnnonce($id,$url,$url2,$app){
		//$annonce = Annonce::with('images')->get()->find($id);	//recupere l'annonce de l'id X et findOrFail lance une exception si modele pas trouvé
		//Pourquoi j'ai utilisé des "with" dans les autres fonctions alors que finalement il n'y a pas besoin??? 
		$annonce = Annonce::find($id);

		//echo $annonce;
		//$miniatures = $annonce->images()->where('name', 'like', '%miniature');;
		//$images = $annonce->images; //$images est un tableau donc faire une boucle dans le templates
		$miniatures = Images::where('id_annonce','=', $id)->where('name', 'like', '%miniature')->get();

		$image = Images::where('id_annonce','=', $id)->where('name', 'not like', '%miniature')->first();

		//$images = $annonce::with('images')->where('name', 'like', '%miniature%');


		//creer vue
		if (!empty($annonce)){
			$annonceur = $annonce->annonceur; //recupere le nom de de l'annonceur de l'annonce	
			//$images = Images::where('id_annonce', '=',$id); //récupère images de l'annonce?

			//var_dump($images);
			$v = new ViewAnnonce($annonce, $annonceur,$url,$url2,$miniatures,$image);
			$v->display();
		}else{
			$app->response->redirect($app->urlFor('annonces'), 303);
		}
	}

	//function pour afficher les images, appeler une vue, créer un block???????
	public function displayImage($name){
		$images = Images::where('name', '=', $name);
		if (stripos($images['name'], 'miniature')){ //si c'est une miniature
			echo "<img src='./data/img_annonces_miniatures/".$images['name'].".".$images['extension']."' alt='image'>";
		}else{
			echo "<img src='./data/img_annonces/".$images['name'].".".$images['extension']."' alt='image'>";		
		}
	}



	//Affichage d'une liste d'annonce
	public function displayAllAnnonce($url){
		$annonceAll = Annonce::with('categorie')->orderBy('id', 'DESC')->get();
		
		$categorie = Categorie::all();
		//creer vue
		$v = new ViewAllAnnonce($annonceAll, $url,$categorie,null);
		$v->display();
	}



	//Affichage du formulaire ajout annonce
	public function displayAddAnnonce(){
		//session_start(); 
		$categories = Categorie::all();
		//creer vue
		$v = new ViewAddAnnonce($categories); //n'est censé prendre aucun parametre
		$v->display();
	}


	//Ajout d'une annonce dans la base
	public function addAnnonce(){
		//session_start();
		$crsfGuard = new CRSFGuard();
		if ($crsfGuard::csrfguard_validate_token($_POST['CSRFName'], $_POST['CSRFToken'])){//test si le token en session correposnd à celui du POST
			if (isset($_POST['nom'], $_POST['mail'], $_POST['tel'], $_POST["password"],$_POST["titre"], $_POST["description"], $_POST["tarif"], $_POST["ville"], $_POST["CP"],$_POST["categorie"] )
			&& !empty($_POST['nom']) && !empty($_POST['mail']) && !empty($_POST['tel']) && !empty($_POST["password"]) && !empty($_POST["titre"]) && !empty( $_POST["description"]) && !empty( $_POST["tarif"]) && !empty( $_POST["ville"]) && !empty( $_POST["CP"]) && !empty($_POST["categorie"])
			&& filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL) 
			){

				//infos annonceur
				/*Faut tester si le mail existe déja, si il existe.... si non on le créer?*/
				/*$mailAnnonceur = Annonceur::where('mail', '=', $_POST['mail'])->first();
				if ($mailAnnonceur == null){
					$annonceur = new Annonceur;
					$annonceur->nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING); //$_POST['nom']; 
					$annonceur->mail = $_POST['mail'];
					$annonceur->tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);//$_POST['tel'];
					// $annonceur->save(); 
					// $id_annonceur = $annonceur->id;

					$condition = true;
				}else{
					$annonceur = $mailAnnonceur;
					$condition = false;
				}*/

				if(!isset($_SESSION['id'])){
					//HASH
					$password =  filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);//$_POST["password"];
					$hash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
				}else{
					$hash = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
				}

				//info categorie
				$categoriePost = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);//$_POST["categorie"];
				$categorie = Categorie::where('nom', '=', $categoriePost)->first();
				$id_categorie = $categorie->id;

				//Info annonce
				$annonce = new Annonce;
				$annonce->password = $hash;
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
				// $annonce->save();
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
				
			}else{
				
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('add'));
			}

			//Sur quel bouton on clique Submit ou Preview 
			if (isset($_POST['Submit'])){
				//SAVE
				//$id_annonceur = $annonceur->id;
				//$annonce->id_annonceurs = $id_annonceur;
				$annonce->save();
				$id_annonce = $annonce->id;
				
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
						$app = \Slim\Slim::getInstance();
						$app->response->redirect($app->urlFor('annonce', array('n' => $id_annonce)));
					}
				} catch (\Exception $e) {
				    // Fail!
				   // $errors = $file->getErrors(); //On sait pas ce que ça fait! 
				    $app = \Slim\Slim::getInstance();
					$app->response->redirect($app->urlFor('annonce', array('n' => $id_annonce)));
				}

				//image ?
			}else if (isset($_POST['Preview'])){
				//PREVIEW
				//session_start();
				$arrayPost = array();
				$arrayPost['annonce'] = $annonce;
				$_SESSION['preview'] = $arrayPost;

				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('add_preview'));
			}
		}

	}

	//Preview annonce
	public function addPreviewAnnonce(){
		//session_start();
		$v = new ViewPreviewAnnonce($_SESSION['preview']);
		$v->display();
	}



	//Ajout d'une annonce dans la base
	public function addPreviewAddAnnonce(){
		//session_start();
		
		//Info annonce
		$annonce = new Annonce;
		$annonce->password = $_SESSION['preview']['annonce']['password'];
		$annonce->titre =  $_SESSION['preview']['annonce']['titre'];//$_POST["titre"];
		$annonce->description =  $_SESSION['preview']['annonce']['description'];//$_POST["description"];
		$annonce->tarif =  $_SESSION['preview']['annonce']['tarif'];//$_POST["tarif"];
		$annonce->ville =  $_SESSION['preview']['annonce']['ville'];//$_POST["ville"];
		$annonce->CP =  $_SESSION['preview']['annonce']['CP'];//$_POST["CP"];
		//$annonce->id_annonceurs = $id_annonceur;
		$annonce->id_categorie = $_SESSION['preview']['annonce']['id_categorie'];
		$annonce->nom = $_SESSION['preview']['annonce']['nom']; //$_POST['nom']; 
		$annonce->mail = $_SESSION['preview']['annonce']['mail']; //$_POST['mail'];
		$annonce->tel = $_SESSION['preview']['annonce']['tel'];//$_POST['tel'];
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
				$app = \Slim\Slim::getInstance();
				$app->response->redirect($app->urlFor('annonce', array('n' => $id_annonce)));
			}
		} catch (\Exception $e) {
		    // Fail!
		   // $errors = $file->getErrors(); //On sait pas ce que ça fait! 
		    $app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('annonce', array('n' => $id_annonce)));
		}

	}


	//Affichage des annonces en fonction d'une catégorie
	public function displayAnnonceByCat($cat, $url){
		$categorie = Categorie::where('nom', '=', $cat)->first();
		$idcategorie = $categorie->id;
		$categorieAll = Categorie::all();


		$annonce =  Annonce::where('id_categorie', '=',$idcategorie)->orderBy('id', 'DESC')->get();
		if (!empty($annonce)){
			//creer vue
			$v = new ViewAnnonceByCat($annonce, $url, $categorieAll);
			$v->display();
		}else{
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('accueil'));
		}
	}

	//Traitement recherche
	public function search(){
		if (isset($_POST['tags'], $_POST['price'] , $_POST['category'])){
			//test recherche par categorie
			$fields['cat'] = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
			//test recherche par prix
			$fields['price'] = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
			//test recherche par mot
			$fields['tags'] = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
			if($fields['tags']==''){
				$fields['tags']='noTags';
			}

			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('search', array('t' => $fields['tags'],'p' => $fields['price'],'c' => $fields['cat'])));
		}else{
			$app = \Slim\Slim::getInstance();
			$app->response->redirect($app->urlFor('search'));
		}
	}

	//Afficher resultat recherche
	public function displaySearch($url,$cat,$t,$p,$c){
		if($t!=null && $p!=null && $c!=null){
			$idcategorie = null;
			//Recherche de la categorie 
			if($c!='allCategories'){
				$categorie_search = Categorie::where('nom', '=', $c)->first();
				$idcategorie = $categorie_search->id;
			}
			//Recherche de toute les categories
			$categorie = Categorie::all();
			$annonceSearch = Annonce::with('images','categorie')->where(function($query) use($p,$t,$c,$idcategorie) {
																	if($t!='noTags'){
			        													$query->where('titre', 'LIKE', "%$t%");
			    													}
																	if($c!='allCategories'){
																        $query->where('id_categorie', '=', "$idcategorie");
																    }
			    													if($p!='allPrice'){
																        $query->where('tarif', '<', "$p");
																    }})
																->orderBy('id', 'DESC')
																->get();
			$v = new ViewSearch($annonceSearch, $url,$categorie);
			$v->display();
		}else{
			$annonceAll = Annonce::with('images','categorie')->orderBy('id', 'DESC')->get();
			$categorie = Categorie::all();
			$v = new ViewSearch($annonceAll, $url,$categorie);
			$v->display();
		}
	}


	//Affichage du formulaire permettant de rentrer son adresse mail pour accéder à ses annonces
	public function displayFormMyAnnonce($formulaire_post){
		//creer vue qui affiche le formulaire avec pour action $formulaire_post
		$v = new ViewFormMyAnnonce($formulaire_post);
		$v->display();
	}

	
	//Affichage des annonces d'une adresse mail en particulier
	public function displayMyAnnonce($url){
		$annonceur_mail = $_POST['mail'];
		/*$annonceur = Annonceur::where('mail', '=',$annonceur_mail)->first();
		$annonceur_id = $annonceur->id;*/
	
		$annonces = Annonce::where('mail', '=', $annonceur_mail)->orderBy('id', 'DESC')->get();


		//creer vue qui affiche le formulaire avec pour action $formulaire_post
		$v = new ViewMyAnnonce($annonces,$url);
		$v->display();
	}


	//Affichage du formulaire permettant de modifier son annonce
	public function displayFormChangeAnnonce($n,$url,$app){
		$annonce = Annonce::find($n);
		$categories = Categorie::all();

		//password
		$hash = $annonce->password;
		$annonceur_password_clear = $_POST['password'];
		
		if (password_verify($annonceur_password_clear, $hash)) {
	        /* Valid */
	        $v = new ViewFormChangeAnnonce($annonce,$url,$categories);
			$v->display();
	    } else {
	        /* Invalid */
	        $app->response->redirect($app->urlFor('annonces'), 303);
	        //faire un retour vers l'affichage de l'annonce ! En appelant la view directement ou la function ???
	    }
	}

	//Modification de l'annonce == PUT
	public function ChangeAnnonce($n,$app){

		$categoriePost = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);//$_POST["categorie"];
		$categorie = Categorie::where('nom', '=', $categoriePost)->first();
		$id_categorie = $categorie->id;

		//gestion informations de l'annonce
		$changeAnnonce = Annonce::find($n);
		$changeAnnonce->titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);//$_POST["titre"];
		$changeAnnonce->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);//$_POST["description"];
		$changeAnnonce->tarif = filter_input(INPUT_POST, 'tarif', FILTER_SANITIZE_STRING);//$_POST["tarif"];
		$changeAnnonce->ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);//$_POST["ville"];
		$changeAnnonce->CP = filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);//$_POST["CP"];
		$changeAnnonce->id_categorie = $id_categorie;
		$changeAnnonce->save();
		$id_annonce = $changeAnnonce->id;


		

		//gestion image
		if(isset($_FILES['image']) && !empty($_FILES["image"])){
			//$nomImg = $_POST["titre"]."test";
			$nomImg = "".$id_annonce."_".time();//génère un nom aléatoir en fonction de l'id et du timestamp
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );//détermine les extensions de fichier autorisées
			$extension_upload = strtolower(  substr(  strrchr($_FILES['image']['name'], '.')  ,1)  ); //récupère l'extension du fichier uploadé

			if (in_array($extension_upload, $extensions_valides)) { //Si l'extension de fichier est dans le tableau des extensions autorisées
			    $path = "data/img_annonces/{$nomImg}.{$extension_upload}"; //chemin ou sera stocké l'image sur le serveur
				$resultat = move_uploaded_file($_FILES['image']['tmp_name'],$path); //upload l'image et la stocke dans le nouveaun chemin, renvoit TRUE en cas de réussite
				if ($resultat){ //Si l'image s'est bien uploadée, on rentre les infos dans la BDD
					$image = Images::where('id_annonce','=', $n)->get();
					foreach ($image as $key => $value) {
						$value->name = $nomImg;
						$value->extension = $extension_upload;
						$value->id_annonce = $id_annonce;
						$value->save();
						echo "Transfert réussi";
					}	
				}
			}//else avec un message d'erreur?
		}
		$app->response->redirect($app->urlFor('annonce', array('n' => $n)), 303);

	}


	//Delete de l'annonce == DELETE
	public function DeleteAnnonce($n,$app){
		$annonce = Annonce::find($n);
		
		//password
		$hash = $annonce->password;
		$annonceur_password_clear = $_POST['password'];
		

		if (password_verify($annonceur_password_clear, $hash)){
			//delete l'annonce
			$annonce->delete();
			//delete les images associés à l'annonce
			$image = Images::where('id_annonce','=', $n)->get();
			foreach ($image as $key => $value) {
				$value->delete();
				$app->response->redirect($app->urlFor('annonces'), 303);
			}
		}else{
			$app->response->redirect($app->urlFor('annonces'), 303);
		}
	}

}