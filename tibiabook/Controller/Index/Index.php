<?php

namespace tibiabook\Controller\Index;

use tibiabook\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as de;

class Index extends Controller {

	protected $controller = 'Index';

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->viewName = 'Index';

		$mustache = array(
			'{{teste}}' => ''
		);

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}