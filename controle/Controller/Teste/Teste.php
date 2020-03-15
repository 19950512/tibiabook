<?php


namespace controle\Controller\Teste;


use Controller\Controller;
use Model\Core\View as View;

class Teste extends Controller
{
    protected $controller = 'Teste';

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){

        $this->viewName = 'Teste';

        $mustache = array();
        
	    // Render View
	    $this->render($mustache, $this->controller, $this->viewName);
    }

    public function otheraction(){

        $this->viewName = 'Otheraction';

        $mustache = array();
	
	    // Render View
	    $this->render($mustache, $this->controller, $this->viewName);
    }
}