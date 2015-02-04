<?php

namespace media\view;

class ViewAnnonceurProfil extends ViewMain {

   protected $obj2;
   protected $obj3;
   protected $obj4;
	public function __construct($d,$x,$y,$z) { //peut etre faire passer un tableau
		parent::__construct($d,$x,$y,$z);
		$this->obj2 = $x;
		$this->obj3 = $y;
		$this->obj4 = $y;
		
		$this->layout = 'profilAnnonceur.html.twig'; //mettre document.twig

		//parametre a passer ?! surement une meilleure facon
		$this->arrayVar['Annonceur'] = $d ;
		$this->arrayVar['Categorie'] = $x;
		$this->arrayVar['Annonces'] = $y;
		$this->arrayVar['Token'] = $z;
		$this->arrayVar['urlAnnonce'] = \Slim\Slim::getInstance()->urlFor('annonce', array('n' => ''));
	}
}