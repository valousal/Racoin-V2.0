<?php

namespace media\view;

use \media\script\CRSFGuard ;

class ViewAddAnnonce extends ViewMain {

	public function __construct($d) { //peut etre faire passer un tableau
		parent::__construct($d);
		$this->layout = 'add.html.twig'; //mettre document.twig

		//parametre a passer ?! surement une meilleure facon
		$this->arrayVar['Categories'] = $d ;
		$this->arrayVar['urlAnnonce'] = \Slim\Slim::getInstance()->urlFor('add_post');
		//$this->arrayVar['urlAnnonce'] = "http://localhost/racoin_old/racoin.net/api/annonces?apiKey=kgDKQdvhmrJVFjL69pHBZMTNftP824";

		if(isset($_SESSION['id'])){
			$authentification = $this->addVar('authentification', $_SESSION); //dans le constructeur ???
		}
	}



	public function render() {
		$res = parent::render();
		$crsfGuard = new CRSFGuard();
		$res = $crsfGuard::csrfguard_replace_forms($res);

		return $res;
	}

}