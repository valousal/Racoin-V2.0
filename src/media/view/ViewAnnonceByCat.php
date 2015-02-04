<?php

namespace media\view;

class ViewAnnonceByCat extends ViewMain {

   protected $obj2;
   protected $obj3;
	public function __construct($d, $x, $y) { //peut etre faire passer un tableau
		parent::__construct($d, $x, $y);
		$this->obj2 = $x;
		$this->obj3 = $y;
		
		$this->layout = 'annoncesByCat.html.twig'; //mettre document.twig

		if (!empty($d)){
			//parametre a passer ?! surement une meilleure facon
			$this->arrayVar['AnnonceByCat'] = $d ;
			$this->arrayVar['url'] = $x ;
			$this->arrayVar['Categorie'] = $y ;
		}else{
			$this->arrayVar['alert'] = 'Aucune annonce dans cette categorie';
		}
	}
}