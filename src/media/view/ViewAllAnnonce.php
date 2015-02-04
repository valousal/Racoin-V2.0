<?php

namespace media\view;

class ViewAllAnnonce extends ViewMain {

   protected $obj2;
   protected $obj3;
   protected $obj4;

	public function __construct($d, $x, $z,$y) { //peut etre faire passer un tableau
		parent::__construct($d, $x, $z,$y);
		$this->obj2 = $x;
		$this->obj3 = $z;
		$this->obj4 = $y;

		$this->layout = 'annonces.html.twig'; //mettre document.twig

		//$this->addVar??

		//parametre a passer ?! surement une meilleure facon
		$this->arrayVar['AnnonceAll'] = $d ;
		$this->arrayVar['Categorie'] = $z;
		$this->arrayVar['url'] = $x ;
		$this->arrayVar['imagePrincipal'] = $y ;
	}
}