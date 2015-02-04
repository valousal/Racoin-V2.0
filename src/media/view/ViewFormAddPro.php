<?php

namespace media\view;

class ViewFormAddPro extends ViewMain {

   // protected $obj2;
	public function __construct($d) { //peut etre faire passer un tableau
		parent::__construct($d);
		// $this->obj2 = $x;
		
		$this->layout = 'addPro.html.twig'; //mettre document.twig

		//parametre a passer ?! surement une meilleure facon
		$this->arrayVar['Categories'] = $d ;
		$this->arrayVar['url'] = \Slim\Slim::getInstance()->urlFor('AddPro');
	}
}