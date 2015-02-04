<?php

namespace media\view;

class ViewAnnonce extends ViewMain {
    protected $obj2;
    protected $obj3;
    protected $obj4;
    protected $obj5;
    protected $obj6;

	public function __construct($d, $x, $y, $z, $i,$ip) {
		parent::__construct($d, $x, $y, $z, $i,$ip); 
		$this->obj2 = $x;
		$this->obj3 = $y;
		$this->obj4 = $z;
		$this->obj5 = $i;
		$this->obj6 = $ip;

		$this->layout = 'annonce.html.twig'; //mettre document.twig

		//parametre a passer
		$this->arrayVar['Annonce'] = $d;
		$this->arrayVar['Annonceur'] = $x; //meilleur solution pour faire passer plusieurs parametre a la vue ???
		$this->arrayVar['Miniatures'] = $i;
		$this->arrayVar['Images'] = $ip; 
		$this->arrayVar['url'] = $y;
		$this->arrayVar['url2'] = $z;
	}
}