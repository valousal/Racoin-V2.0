<?php

namespace media\view;

use \media\script\CRSFGuard ;

class ViewTokenApi extends ViewMain {

	public function __construct() { //peut etre faire passer un tableau
		parent::__construct();
		$this->layout = 'TokenApi.html.twig'; //mettre document.twig

		$this->arrayVar['token'] = $_SESSION['token'];

	}

}