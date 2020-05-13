<?php

use Model\Router\Router AS Router;
use Model\Core\De AS de;

class Aplication {

	protected $router;

	function __construct(){

		$this->router = new Router();

		// Inicia o controlador
		$controller = new $this->router->namespace();

		if(!method_exists($controller, $this->router->action)){
			$this->router->set404();
			$controller = new $this->router->namespace();
		}

		$controller->{$this->router->action}();
	}
}