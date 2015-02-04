<?php

namespace media\view;

class ViewMyAnnonce extends ViewMain {

   protected $obj2;
	public function __construct($d,$x) { //peut etre faire passer un tableau
		parent::__construct($d,$x);
		$this->obj2 = $x;
		
		$this->layout = 'myAnnonce.html.twig'; //mettre document.twig

		$this->arrayVar['MyAnnonces'] = $d ;
		$this->arrayVar['url'] = $x ;
	}
}