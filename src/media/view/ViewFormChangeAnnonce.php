<?php

namespace media\view;

class ViewFormChangeAnnonce extends ViewMain {

   protected $obj2;
   protected $obj3;
	public function __construct($d,$x,$y) { //peut etre faire passer un tableau
		parent::__construct($d,$x,$y);
		$this->obj2 = $x;
		$this->obj3 = $y;
		
		$this->layout = 'change.html.twig'; //mettre document.twig

		$this->arrayVar['annonce'] = $d ;
		$this->arrayVar['url'] = $x ;
		$this->arrayVar['Categories'] = $y ;

	}
} 