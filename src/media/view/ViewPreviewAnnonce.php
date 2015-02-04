<?php

namespace media\view;

class ViewPreviewAnnonce extends ViewMain {
    // protected $obj2;
	public function __construct($d) {
		parent::__construct($d); 
		// $this->obj2 = $x;

		$this->layout = 'preview.html.twig'; //mettre document.twig

		//parametre a passer
		$this->arrayVar['post'] = $d;
		$this->arrayVar['url_change'] = $app = \Slim\Slim::getInstance()->urlFor('add');
		$this->arrayVar['url_valid'] = $app = \Slim\Slim::getInstance()->urlFor('add_preview_add');
	}
}