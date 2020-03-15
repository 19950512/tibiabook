<?php


namespace controle\Controller\Admin;

use controle\Controller\Controller;
use Model\Core\De as de;
use Model\Admin\Admin as adm;
use Model\Visitante\Render as MiniaturaRender;

class Admin extends Controller
{

	protected $controller = 'Admin';
	protected $layout = 'Admin';

	public function __construct()
	{
		parent::__construct();
	}

	public function index(){

		$this->_checkLogin();

		$this->viewName = 'Admin';
	
		$this->view->setTitle('Titulo do Admin');
		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'GSTVara'],
			['name' => 'description', 'content' => 'Chat da Twitch é Brabo D+++']
		]);
	
		$mustache = array(
			'{{pushResposta}}' => ' OK, Maestro'
		);
		
		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header, $this->layout);
	}

	private function _checkLogin(){

        if(!isset($_SESSION[SESSION_LOGIN]['acc_id'])){
            header('location: /admin/login');
        }
	}

	public function login(){
		$this->viewName = 'Login';
	
		$this->view->setTitle('Login');
	
		$mustache = array();
		
		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header, $this->layout);
	}

	public function publicacao(){

		$this->_checkLogin();

		$this->viewName = 'Publicacao';
	
		$this->view->setTitle('Publicacao');
	
		$mustache = array();
		
		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header, $this->layout);
	}

	public function configuracao(){

		$this->_checkLogin();

		$this->viewName = 'Configuracao';
	
		$this->view->setTitle('Configuracao');
	
		$mustache = array();
		
		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header, $this->layout);
	}

	public function visitante(){
		
		$this->_checkLogin();
		
		$this->viewName = 'Visitante';
	
		$this->view->setTitle('Visitante');
	
		$visitantes = $this->visitante->getVisitantes();

		$miniaturaVisitante = $this->view->getView('Admin', 'MiniaturaVisitante');

		$mustache = [
			'{{miniaturas-visitantes}}' => MiniaturaRender::miniatura($visitantes['data'], $miniaturaVisitante),
			'{{total-visitantes}}' => count($visitantes['data'] ?? [])
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header, $this->layout);
	}



	public function autentica(){

		if(isset($_POST['acc_id'], $_POST['acc_password']) AND !empty($_POST['acc_id']) AND !empty($_POST['acc_password'])){

			$acc_id 		= $_POST['acc_id'] ?? '';
			$acc_password 	= $_POST['acc_password'] ?? '';

			$admin = new adm;
			$resposta = $admin->autentica($acc_id, $acc_password);

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}

}