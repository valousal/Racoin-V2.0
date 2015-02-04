<?php

namespace media\view;

use \media\script\CRSFGuard ;

class ViewGenerateTokenApi extends ViewMain {

	public function __construct() { //peut etre faire passer un tableau
		parent::__construct();
		$this->layout = 'displayFormToken.html.twig'; //mettre document.twig

		$this->arrayVar['urlGenerateToken'] = \Slim\Slim::getInstance()->urlFor('generate_token_api');

	}

}