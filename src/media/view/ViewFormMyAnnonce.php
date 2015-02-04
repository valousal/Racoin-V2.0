<?php

namespace media\view;

class ViewFormMyAnnonce extends ViewMain {

   //protected $obj2;
	public function __construct($d) { //peut etre faire passer un tableau
		parent::__construct($d);
		//$this->obj2 = $x;
		
		$this->layout = 'myannonceForm.html.twig'; //mettre document.twig

		$this->arrayVar['action'] = $d ;
	}
} 