<?php

namespace tibiabook\Controller\Folha;

use tibiabook\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as De;

class Folha extends Controller {

	protected $controller = 'Folha';

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->viewName = 'Folha';

		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'DevNux'],
		]);

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}