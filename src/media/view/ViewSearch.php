<?php

namespace media\view;

class ViewSearch extends ViewMain {

   protected $obj2;
   protected $obj4;
	public function __construct($d,$x,$z) { //peut etre faire passer un tableau
		parent::__construct($d,$x,$z);
		$this->obj2 = $x;
		$this->obj4 = $z;
		
		$this->layout = 'search.html.twig'; //mettre document.twig

		//parametre a passer ?! surement une meilleure facon
		
		$this->arrayVar['Annonces'] = $d ;
		$this->arrayVar['url'] = $x ;
		$this->arrayVar['categorie'] = $z ;



	}
} 