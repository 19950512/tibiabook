<?php

namespace controle\Controller\Index;

use controle\Controller\Controller;
use Model\Core\View as View;

class Index extends Controller
{

	protected $controller = 'Index';

	public function __construct()
	{
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