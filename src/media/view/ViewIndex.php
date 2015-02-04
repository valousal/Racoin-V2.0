<?php

namespace media\view;

class ViewIndex extends ViewMain {

   protected $obj2;
   protected $obj4;
	public function __construct($d,$x,$z,$e) { //peut etre faire passer un tableau
		parent::__construct($d,$x,$z,$e);
		$this->obj2 = $x;
		$this->obj4 = $z;
		
		$this->layout = 'index.html.twig'; //mettre document.twig

		//parametre a passer ?! surement une meilleure facon
		
		$this->arrayVar['AnnonceLast'] = $e ;
		$this->arrayVar['AnnonceAll'] = $d ;
		$this->arrayVar['url'] = $x ;
		$this->arrayVar['categorie'] = $z ;



	}
} 